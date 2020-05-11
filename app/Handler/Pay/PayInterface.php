<?php
/**
 * Created by PhpStorm.
 * User: 简美
 * Date: 2020/4/2
 * Time: 12:49
 */

namespace App\Handler\Pay;


interface PayInterface
{
    /**
     * @param string $no *流水号
     * @param float $total_amount *金额
     * @param string $subject *支付标题
     * @return mixed
     */
    public function webPay(string $no, float $total_amount, string $subject);

    /**
     * @return array
     */
    public function verify($data = null);

    /**
     * 向服务器发送成功回调
     * @return mixed
     */
    public function success();

    /**
     * 退款
     * @param string $no *订单流水号
     * @param float $total_amount *退款金额
     * @param string $refundNo *退款流水号
     * @return mixed
     */
    public function refund(string $no, float $total_amount, string $refundNo);
}