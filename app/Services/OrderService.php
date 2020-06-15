<?php
/**
 * Created by PhpStorm.
 * User: 简美
 * Date: 2020/4/10
 * Time: 10:32
 */

namespace App\Services;

use App\Exception\ServiceException;
use App\Facade\Redis;
use App\Model\CrowdfundingProduct;
use App\Model\Order;
use App\Model\OrderItem;
use App\Model\ProductSku;
use App\Model\User;
use App\Model\UserAddress;
use Carbon\Carbon;
use Hyperf\DbConnection\Db;
use Hyperf\Di\Annotation\Inject;

class OrderService
{
    /**
     * @Inject()
     * @var OrderQueueService
     */
    private $orderQueueService;

    /**
     * @Inject()
     * @var AliPayService
     */
    private $aliPayService;

    /**
     * @Inject()
     * @var WeChatPayService
     */
    private $wecChatPayService;

    /**
     * @Inject()
     * @var CouponCodeService
     */
    private $couponService;

    /**
     * @Inject()
     * @var EmailQueueService
     */
    private $emailQueueService;

    public function createOrder(User $user, $orderData): Order
    {
        $order = Db::transaction(function () use ($user, $orderData)
        {
            //更新收货地址最后使用日期
            $address = UserAddress::getFirstById($orderData['address_id']);
            $address->update(['last_used_at' => Carbon::now()]);
            //填充订单
            $order = new Order([
                'type' => Order::TYPE_NORMAL,
                'address' => [
                    'address' => $address->full_address,
                    'zip' => $address->zip,
                    'contact_name' => $address->contact_name,
                    'contact_phone' => $address->contact_phone,
                ],
                'remark' => array_key_exists('remark', $orderData) ? $orderData['remark'] : '',
                'total_amount' => 0
            ]);
            $order->user()->associate($user);
            $order->save();

            $totalAmount = 0;
            $skuIds = [];
            //插入子订单
            foreach ($orderData['items'] as $data)
            {
                $skuIds[] = $data['sku_id'];
                $productSku = ProductSku::getFirstById($data['sku_id']);

                /**@var $item \App\Model\OrderItem * */
                $item = $order->items()->make([
                    'price' => $productSku->price,
                    'amount' => $data['amount'],
                ]);

                $item->product()->associate($productSku->product_id);
                $item->productSku()->associate($productSku);
                $item->save();

                $totalAmount += $productSku->price * $item['amount'];

                if ($productSku->decreaseStock($data['amount']) <= 0)
                {
                    throw new ServiceException(403, "{$productSku->title},库存不足");
                };
            }
            //使用了优惠券，检查优惠券
            if (array_key_exists('code', $orderData))
            {
                $couponCode = $this->couponService->checkCouponCode($orderData['code']);
                $totalAmount = $couponCode->getAdjustedPrice($totalAmount);
                //检查订单是否满足优惠券最低金额
                if ($totalAmount < $couponCode->min_amount)
                {
                    throw new ServiceException(403, "满{$couponCode->min_amount}元才可使用");
                }
                $order->couponCode()->associate($couponCode);
                if ($couponCode->changeUsed() <= 0)
                {
                    throw new ServiceException(403, '优惠券已经发光');
                }
            }
            $order->update(['total_amount' => $totalAmount]);
            $this->orderQueueService->pushCloseOrderJod($order, 500);
            //清空购物车
            $user->cartItems()->whereIn('product_sku_id', $skuIds)->delete();

            return $order;
        });

        return $order;
    }

    /**
     * 秒杀
     * @param User $user
     * @param UserAddress $address
     * @param ProductSku $sku
     * @return mixed
     */
    public function seckill(User $user, $orderData)
    {
        $order = DB::transaction(function () use ($user, $orderData)
        {
            $productSku = ProductSku::getFirstById($orderData['sku_id']);
            // 创建一个订单
            $address = $orderData['address'];
            $order = new Order([
                'address' => [ // 将地址信息放入订单中
                    'address' => $address['province'] . $address['city'] . $address['district'] . $address['address'],
                    'zip' => $address['zip'],
                    'contact_name' => $address['contact_name'],
                    'contact_phone' => $address['contact_phone'],
                ],
                'remark' => '',
                'total_amount' => $productSku->price,
                'type' => Order::TYPE_SECKILL,
            ]);
            // 订单关联到当前用户
            $order->user()->associate($user);
            // 写入数据库
            $order->save();
            // 创建一个新的订单项并与 SKU 关联
            $item = $order->items()->make([
                'amount' => 1, // 秒杀商品只能一份
                'price' => $productSku->price,
            ]);
            $item->product()->associate($productSku->product_id);
            $item->productSku()->associate($productSku);
            $item->save();
            // 扣减对应 SKU 库存
            if ($productSku->decreaseStock(1) <= 0)
            {
                throw new ServiceException(403, '该商品库存不足');
            }
            Redis::decr('seckill_sku_'.$productSku->id);
            return $order;
        });
        // 秒杀订单的自动关闭时间与普通订单不同
        $this->orderQueueService->pushCloseOrderJod($order, 100);

        return $order;
    }

    /**
     * 众筹下单
     * @param User $user
     * @param $orderData
     * @return Order
     */
    public function crowdfunding(User $user, $orderData): Order
    {
        $order = Db::transaction(function () use ($user, $orderData)
        {
            //更新收货地址最后使用日期
            $address = UserAddress::getFirstById($orderData['address_id']);
            $address->update(['last_used_at' => Carbon::now()]);
            //填充订单
            $order = new Order([
                'type' => Order::TYPE_CROWDFUNDING,
                'address' => [
                    'address' => $address->full_address,
                    'zip' => $address->zip,
                    'contact_name' => $address->contact_name,
                    'contact_phone' => $address->contact_phone,
                ],
                'remark' => array_key_exists('remark', $orderData) ? $orderData['remark'] : '',
                'total_amount' => 0
            ]);
            $order->user()->associate($user);
            $order->save();

            $totalAmount = 0;
            $skuIds = [];
            $crowdfunding = null;
            //插入子订单
            foreach ($orderData['items'] as $data)
            {
                $skuIds[] = $data['sku_id'];
                $productSku = ProductSku::getFirstById($data['sku_id']);
                $crowdfunding = $productSku->product->crowdfunding;
                /**@var $item \App\Model\OrderItem * */
                $item = $order->items()->make([
                    'price' => $productSku->price,
                    'amount' => $data['amount'],
                ]);

                $item->product()->associate($productSku->product_id);
                $item->productSku()->associate($productSku);
                $item->save();

                $totalAmount += $productSku->price * $item['amount'];

                if ($productSku->decreaseStock($data['amount']) <= 0)
                {
                    throw new ServiceException(403, "{$productSku->title},库存不足");
                };
            }

            /** @var  $crowdfunding CrowdfundingProduct */
            $timeOutTtl = $crowdfunding->end_time->getTimestamp() - time();
            $order->update(['total_amount' => $totalAmount]);
            $this->orderQueueService->pushCloseOrderJod($order, $timeOutTtl);

            return $order;
        });

        return $order;
    }

    /**
     * 订单发货
     * @param Order $order *订单
     * @param string $company *物流公司
     * @param string $express_no *物流单号
     */
    public function sendOutGood(Order $order, string $company, string $express_no)
    {
        if (!$order->paid_at)
        {
            throw new ServiceException(403, '该订单未付款');
        }
        if ($order->ship_status != Order::SHIP_STATUS_PENDING)
        {
            throw new ServiceException(403, '该订单已经发货');
        }
        if ($order->type === Order::TYPE_CROWDFUNDING && $order->crowdfunding_status !== CrowdfundingProduct::STATUS_SUCCESS)
        {
            throw new ServiceException(403, '众筹成功后才可以发货');
        }

        $order->update([
            'ship_status' => Order::SHIP_STATUS_DELIVERED,
            'ship_data' => [
                'company' => $company,
                'express_no' => $express_no
            ],
        ]);


        /** @var  $user User */
        $user = $order->user;
        if ($user->email)
        {
            $jobParams = [
                'subject' => "您好，您的订单已经发货",
                'body' => "{$user->user_name}您好，您的订单已经发货，物流公司为:{$company}，订单号:{$express_no}",
                'email' => $user->email,
                'isHtml' => true
            ];
            $this->emailQueueService->pushSendEmailJob($jobParams, 0);
        }
    }

    /**
     * 确认收货
     * @param Order $order *订单
     */
    public function receivedGood(Order $order)
    {
        if (authUser()->id != $order->user_id)
        {
            throw new ServiceException(403, '无权限操作此订单');
        }

        if ($order->ship_status != Order::SHIP_STATUS_DELIVERED)
        {
            throw new ServiceException(403, '订单物流状态异常');
        }

        $order->update([
            'ship_status' => Order::SHIP_STATUS_RECEIVED
        ]);
    }

    /**
     * 评价商品
     * @param Order $order *订单号
     * @param array $reviews *评论数据
     */
    public function review(Order $order, array $reviews)
    {
        if (!$order->paid_at)
        {
            throw new ServiceException(403, '该订单未付款');
        }
        if ($order->ship_status != Order::SHIP_STATUS_RECEIVED)
        {
            throw new ServiceException(403, '订单没收货');
        }
        if ($order->reviewed)
        {
            throw new ServiceException(403, '订单已经评论');
        }

        Db::transaction(function () use ($order, $reviews)
        {
            foreach ($reviews as $review)
            {
                $orderItem = OrderItem::getFirstById($review['id']);
                $orderItem->update([
                    'rating' => $review['rating'],
                    'review' => $review['review'],
                    'reviewed_at' => Carbon::now()
                ]);
            }

            $order->reviewed = true;
            $order->save();
        });
    }

    /**
     * 申请退款
     * @param Order $order *订单
     * @param $reason *理由
     */
    public function applyRefund(Order $order, $reason)
    {
        if (!$order->paid_at)
        {
            throw new ServiceException(403, '该订单未付款');
        }
        if ($order->user_id !== authUser()->id)
        {
            throw new ServiceException(403, '没有权限操作此订单');
        }
        if ($order->type === Order::TYPE_CROWDFUNDING)
        {
            throw new ServiceException(403, '众筹订单不允许主动退款');
        }
        if ($order->refund_status !== Order::REFUND_STATUS_PENDING)
        {
            throw new ServiceException(403, '订单已经申请过退款');
        }


        $extra = $order->extra ?: [];
        $extra['refund_reason'] = $reason;
        $order->update([
            'refund_status' => Order::REFUND_STATUS_APPLIED,
            'extra' => $extra
        ]);
    }

    /**
     * 拒绝申请退款
     * @param Order $order *订单
     * @param $reason *拒绝退款理由
     */
    public function refuseRefund(Order $order, $reason)
    {
        $this->checkOrderRefundStatus($order);

        $extra = $order->extra ?: [];
        $extra['refund_disagree_reason'] = $reason;
        $order->update([
            'refund_status' => Order::REFUND_STATUS_PENDING,
            'extra' => $extra
        ]);

        //发送通知
        /** @var  $user User */
        $user = $order->user;
        if ($user->email)
        {
            $jobParams = [
                'subject' => "申请退款被拒绝",
                'body' => "{$user->user_name}您好，您的订单{$order->no}因为{$reason}，被拒绝退款",
                'email' => $user->email,
                'isHtml' => true
            ];
            $this->emailQueueService->pushSendEmailJob($jobParams, 0);
        }
    }

    public function refund(Order $order)
    {
        $this->checkOrderRefundStatus($order);

        switch ($order->payment_method)
        {
            case 'installment':
                $order->update([
                    'refund_no' => getUUID('refund'), // 生成退款订单号
                    'refund_status' => Order::REFUND_STATUS_PROCESSING, // 将退款状态改为退款中
                ]);
                // 触发退款异步任务
                $this->orderQueueService->pushRefundInstallmentOrderJob($order, 5);
                break;
            case 'alipay':
                //耗时长，后期修改为异步任务
                $this->aliPayService->refund($order);
                break;
            case 'wechat':
                $this->wecChatPayService->refund($order);
                break;
            default:
                throw new ServiceException(403, '未知支付方式');
        }

        return true;
    }

    public function checkOrderRefundStatus(Order $order)
    {
        if (!$order->paid_at)
        {
            throw new ServiceException(403, '该订单未付款');
        }
        if ($order->refund_status !== Order::REFUND_STATUS_APPLIED)
        {
            throw new ServiceException(403, '订单没有申请退款');
        }
    }
}