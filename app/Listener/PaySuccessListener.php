<?php

declare(strict_types=1);

namespace App\Listener;

use App\Event\PaySuccessEvent;
use App\Model\Order;
use App\Model\OrderItem;
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

    public function process(object $event)
    {
        /** @var $order Order */
        $order = $event->order;

        foreach ($order->items() as $orderItem)
        {
            /** @var $orderItem OrderItem */
            $product = $orderItem->product();
        }
    }
}
