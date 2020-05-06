<?php
/**
 * Created by PhpStorm.
 * User: 简美
 * Date: 2020/4/10
 * Time: 10:32
 */

namespace App\Services;

use App\Exception\ServiceException;
use App\Handler\Pay\PayFactory;
use App\Model\Order;
use Carbon\Carbon;
use Hyperf\Di\Annotation\Inject;
use Phper666\JwtAuth\Jwt;

class AliPayService
{
    private $pay;

    /**
     * @Inject()
     * @var Jwt
     */
    private $jwt;

    public function __construct(PayFactory $factory)
    {
        $this->pay = $factory->get('alipay');
    }

    /***
     * 支付宝网页支付
     * @param $orderId
     * @return mixed
     */
    public function aliPayWeb($orderId)
    {
        $order = Order::getFirstById($orderId);

        if (!$order || $order->user_id != $this->jwt->getTokenObj()->getClaim('id'))
        {
            throw new ServiceException(403, '订单不存在');
        }

        if ($order->paid_at || $order->closed)
        {
            throw new ServiceException(403, '订单已经关闭');
        }

        return $this->pay->webPay($order->no, $order->total_amount, $order->no)->getContent();
    }

    /**
     * 检验前端支付参数
     * @return array
     */
    public function aliPayWebReturn($data)
    {
        return $this->pay->verify($data);
    }

    public function aliPayNotify($data)
    {
        $no = $data['out_trade_no'];

        $order = Order::getFirstByWhere(['no' => $no]);
        if (!$order)
        {
            //DoSomeThing
            return;
        }

        if ($order->paid_at)
        {
            $this->pay->success();
        }

        $order->update([
            'paid_at' => Carbon::now(), // 支付时间
            'payment_method' => 'alipay', // 支付方式
            'payment_no' => $data['trade_no'], // 支付宝订单号
        ]);

        $this->pay->success();
    }
}