<?php
/**
 * Created by PhpStorm.
 * User: 简美
 * Date: 2020/5/8
 * Time: 17:38
 */

namespace App\Event;


use App\Model\Order;

class RefundSuccessEvent
{
    public $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }
}