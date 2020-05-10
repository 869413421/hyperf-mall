<?php
/**
 * Created by PhpStorm.
 * User: 简美
 * Date: 2020/4/10
 * Time: 10:32
 */

namespace App\Services;

use App\Exception\ServiceException;
use App\Model\Order;
use App\Model\OrderItem;
use App\Model\ProductSku;
use App\Model\User;
use App\Model\UserAddress;
use Carbon\Carbon;
use Hyperf\DbConnection\Db;
use Hyperf\Di\Annotation\Inject;
use Phper666\JwtAuth\Jwt;

class OrderService
{
    /**
     * @Inject()
     * @var OrderQueueService
     */
    private $orderQueueService;

    /**
     * @Inject()
     * @var EmailQueueService
     */
    private $emailQueueService;

    public function createOrder(User $user, $orderData): Order
    {
        $order = Db::transaction(function () use ($user, $orderData)
        {
            $address = UserAddress::getFirstById($orderData['address_id']);
            $address->update(['last_used_at' => Carbon::now()]);

            $order = new Order([
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

            $order->update(['total_amount' => $totalAmount]);
            $this->orderQueueService->pushCloseOrderJod($order, 500);
            $user->cartItems()->whereIn('product_sku_id', $skuIds)->delete();

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

        $order->update([
            'ship_status' => Order::SHIP_STATUS_DELIVERED,
            'ship_data' => [
                'company' => $company,
                'express_no' => $express_no
            ],
        ]);

        /** @var  $user User */
        $user = $order->user;
        $jobParams = [
            'subject' => "您好，您的订单已经发货",
            'body' => "{$user->user_name}您好，您的订单已经发货，物流公司为:{$company}，订单号:{$express_no}",
            'email' => $user->email,
            'isHtml' => true
        ];
        $this->emailQueueService->pushSendEmailJob($jobParams, 0);
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
                var_dump($review);
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

    public function applyRefund(Order $order, $reason)
    {
        if (!$order->paid_at)
        {
            throw new ServiceException(403, '该订单未付款');
        }
        if ($order->ship_status !== Order::SHIP_STATUS_RECEIVED)
        {
            throw new ServiceException(403, '订单没收货');
        }
        if ($order->user_id !== authUser()->id)
        {
            throw new ServiceException(403, '没有权限操作此订单');
        }
        if ($order->refund_status !== Order::REFUND_STATUS_PENDING)
        {
            throw new ServiceException('该订单已经申请过退款，请勿重复申请');
        }

        $extra = $order->extra ?: [];
        $extra['refund_reason'] = $reason;
        $order->update([
            'refund_status' => Order::REFUND_STATUS_APPLIED,
            'extra' => $extra
        ]);
    }

    public function refuseRefund(Order $order, $reason)
    {
        if (!$order->paid_at)
        {
            throw new ServiceException(403, '该订单未付款');
        }
        if ($order->ship_status !== Order::SHIP_STATUS_RECEIVED)
        {
            throw new ServiceException(403, '订单没收货');
        }
        if ($order->refund_status !== Order::REFUND_STATUS_APPLIED)
        {
            throw new ServiceException(403, '订单没有申请退款');
        }

        $extra = $order->extra ?: [];
        $extra['refund_disagree_reason'] = $reason;
        $order->update([
            'refund_status' => Order::REFUND_STATUS_PENDING,
            'extra' => $extra
        ]);
    }
}