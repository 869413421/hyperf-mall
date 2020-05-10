<?php
/**
 * Created by PhpStorm.
 * User: 简美
 * Date: 2020/4/10
 * Time: 10:32
 */

namespace App\Services;

use App\Event\PaySuccessEvent;
use App\Exception\ServiceException;
use App\Handler\Pay\PayFactory;
use App\Model\Order;
use Carbon\Carbon;
use Psr\EventDispatcher\EventDispatcherInterface;
use Hyperf\Di\Annotation\Inject;
use Phper666\JwtAuth\Jwt;

class WeChatPayService
{
    private $pay;

    /**
     * @Inject()
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @Inject()
     * @var Jwt
     */
    private $jwt;

    public function __construct(PayFactory $factory)
    {
        $this->pay = $factory->get('wechat');
    }

    /***
     * 微信扫码支付
     * @param $orderId
     * @return mixed
     */
    public function weChatPayWeb($orderId)
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

    public function weChatPayNotify($data)
    {
        $this->pay->verify($data);
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
            'payment_method' => 'wechat', // 支付方式
            'payment_no' => $data['trade_no'], // 支付宝订单号
        ]);

        $this->pay->success();
        //触发订单支付成功事件
        $this->eventDispatcher->dispatch(new PaySuccessEvent($order));
    }
}