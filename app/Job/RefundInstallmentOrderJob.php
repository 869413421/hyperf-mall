<?php

declare(strict_types=1);

namespace App\Job;

use App\Exception\ServiceException;
use App\Handler\Pay\PayFactory;
use App\Model\Installment;
use App\Model\InstallmentItem;
use App\Model\Order;
use App\Services\AliPayService;
use App\Services\WeChatPayService;
use Hyperf\AsyncQueue\Job;
use Yansongda\Pay\Pay;

class RefundInstallmentOrderJob extends Job
{

    public $order;

    public function __construct(Order $order)
    {
        // 这里最好是普通数据，不要使用携带 IO 的对象，比如 PDO 对象
        $this->order = $order;
    }

    public function handle()
    {
        // 如果商品订单支付方式不是分期付款、订单未支付、订单退款状态不是退款中，则不执行后面的逻辑
        if ($this->order->payment_method !== 'installment'
            || !$this->order->paid_at
            || $this->order->refund_status !== Order::REFUND_STATUS_PROCESSING)
        {
            return;
        }
        // 找不到对应的分期付款，原则上不可能出现这种情况，这里的判断只是增加代码健壮性
        if (!$installment = Installment::query()->where('order_id', $this->order->id)->first())
        {
            return;
        }
        // 遍历对应分期付款的所有还款计划
        foreach ($installment->items as $item)
        {
            // 如果还款计划未支付，或者退款状态为退款成功或退款中，则跳过
            if (!$item->paid_at || in_array($item->refund_status, [
                    InstallmentItem::REFUND_STATUS_SUCCESS,
                    InstallmentItem::REFUND_STATUS_PROCESSING,
                ]))
            {
                continue;
            }
            // 调用具体的退款逻辑，
            try
            {
                $this->refundInstallmentItem($item);
            }
            catch (\Exception $e)
            {
                var_dump($e->getMessage());
                // 假如某个还款计划退款报错了，则暂时跳过，继续处理下一个还款计划的退款
                continue;
            }
        }
        // 设定一个全部退款成功的标志位
        $allSuccess = true;
        // 再次遍历所有还款计划
        foreach ($installment->items as $item)
        {
            // 如果该还款计划已经还款，但退款状态不是成功
            if ($item->paid_at &&
                $item->refund_status !== InstallmentItem::REFUND_STATUS_SUCCESS)
            {
                // 则将标志位记为 false
                $allSuccess = false;
                break;
            }
        }
        // 如果所有退款都成功，则将对应商品订单的退款状态修改为退款成功
        if ($allSuccess)
        {
            $this->order->update([
                'refund_status' => Order::REFUND_STATUS_SUCCESS,
            ]);
        }
    }

    protected function refundInstallmentItem(InstallmentItem $item)
    {
        $factory = container()->get(PayFactory::class);
        $aliPay = $factory->get('alipay');
        var_dump('here');
        //微信不是实时返回要修改回调，所以重新构造一个对象
        $wechatConfig = config('pay.wechat');
        $wechatConfig['notify_url'] = config('host') . '/wechat/installment/pay/refund/service';
        $wechatPay = Pay::wechat($wechatConfig);
        // 退款单号使用商品订单的退款号与当前还款计划的序号拼接而成
        $refundNo = $this->order->refund_no . $item->sequence;
        // 根据还款计划的支付方式执行对应的退款逻辑

        switch ($item->payment_method)
        {
            case 'wechat':
                $wechatPay->refund([
                    'transaction_id' => $item->payment_no, // 这里我们使用微信订单号来退款
                    'total_fee' => $item->total * 100, //原订单金额，单位分
                    'refund_fee' => $item->base * 100, // 要退款的订单金额，单位分，分期付款的退款只退本金
                    'out_refund_no' => $refundNo, // 退款订单号
                    // 微信支付的退款结果并不是实时返回的，而是通过退款回调来通知，因此这里需要配上退款回调接口地址
                ]);
                // 将还款计划退款状态改成退款中
                $item->update([
                    'refund_status' => InstallmentItem::REFUND_STATUS_PROCESSING,
                ]);
                break;
            case 'alipay':

                //用的是分期主订单out_trade_no加上分期的sequence，非子定单trade_no,要注意！！！
                $out_trade_no = $item->installment->no . '_' . $item->sequence;
                $ret = $aliPay->refund($out_trade_no, $item->base, $refundNo);
                // 根据支付宝的文档，如果返回值里有 sub_code 字段说明退款失败
                if ($ret->sub_code)
                {
                    $item->update([
                        'refund_status' => InstallmentItem::REFUND_STATUS_FAILED,
                    ]);
                }
                else
                {
                    // 将订单的退款状态标记为退款成功并保存退款订单号
                    $item->update([
                        'refund_status' => InstallmentItem::REFUND_STATUS_SUCCESS,
                    ]);
                }
                break;
            default:
                // 原则上不可能出现，这个只是为了代码健壮性
                throw new ServiceException(403, '未知订单支付方式：' . $item->payment_method);
                break;
        }
    }
}
