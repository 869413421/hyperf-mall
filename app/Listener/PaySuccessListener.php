<?php

declare(strict_types=1);

namespace App\Listener;

use App\Event\PaySuccessEvent;
use App\Model\Order;
use App\Model\OrderItem;
use App\Model\Product;
use Hyperf\Event\Annotation\Listener;
use Psr\Container\ContainerInterface;
use Hyperf\Event\Contract\ListenerInterface;

/**
 * @Listener(priority=10)
 */
class PaySuccessListener implements ListenerInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function listen(): array
    {
        return [
            PaySuccessEvent::class
        ];
    }

    /**
     * @param object $event
     */
    public function process(object $event)
    {
        /** @var $order Order */
        $order = $event->order;
        foreach ($order->items as $orderItem)
        {
            /** @var $orderItem OrderItem */

            /** @var $product Product */
            $product = $orderItem->product;

            //计算商品销量
            $soleCount = OrderItem::query()
                ->where('product_id', $product->id)
                ->whereHas('order', function ($query)
                {
                    $query->whereNotNull('paid_at');
                })
                ->sum('amount');

            //保存商品销量
            $product->sold_count = $soleCount;
            $product->save();
        }
    }
}
