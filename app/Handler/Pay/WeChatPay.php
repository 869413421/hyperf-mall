<?php
/**
 * Created by PhpStorm.
 * User: 简美
 * Date: 2020/4/23
 * Time: 17:34
 */

namespace App\Handler\Pay;


use Yansongda\Pay\Pay;

class WeChatPay implements PayInterface
{
    private $pay;

    public function __construct($config)
    {
        $this->pay = Pay::wechat($config);
    }

    public function webPay(string $no, float $total_amount, string $subject)
    {
        return $this->pay->scan([
            'out_trade_no' => $no,
            'total_amount' => $total_amount,
            'subject' => $subject
        ]);
    }

    public function verify($data = null)
    {
        return $this->pay->verify($data);
    }

    public function success()
    {
        return $this->pay->success();
    }

    public function refund(string $no, float $total_amount, string $refundNo)
    {
        return $this->pay->refund([
            'out_trade_no' => $no, // 之前的订单流水号
            'total_fee' => $total_amount * 100, //原订单金额，单位分
            'refund_fee' => $total_amount * 100, // 要退款的订单金额，单位分
            'out_refund_no' => $refundNo, // 退款订单号
            // 微信支付的退款结果并不是实时返回的，而是通过退款回调来通知，因此这里需要配上退款回调接口地址
            'notify_url' => config('host') . '/wechat/pay/refund/service'
        ]);
    }

}