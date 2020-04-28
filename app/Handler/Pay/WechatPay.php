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
        return $this->pay->web([
            'out_trade_no' => $no,
            'total_amount' => $total_amount,
            'subject' => $subject
        ]);
    }

    public function verify($data = null)
    {
        // TODO: Implement verify() method.
    }

    public function success()
    {
        // TODO: Implement success() method.
    }
}