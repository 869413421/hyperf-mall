<?php

declare(strict_types=1);

namespace App\Listener;

use App\Event\RefundSuccessEvent;
use App\Model\Order;
use App\Model\User;
use App\Services\EmailQueueService;
use Hyperf\Event\Annotation\Listener;
use Psr\Container\ContainerInterface;
use Hyperf\Event\Contract\ListenerInterface;

/**
 * @Listener(priority=10)
 */
class RefundSuccessListener implements ListenerInterface
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
            RefundSuccessEvent::class
        ];
    }

    /**
     * @param object $event
     */
    public function process(object $event)
    {
        /** @var $order Order */
        $order = $event->order;
        /** @var  $user User */
        $user = $order->user;
        if (!$user->email)
        {
            return;
        }

        $emailQueueService = $this->container->get(EmailQueueService::class);
        $jobParams = [
            'subject' => "退款成功",
            'body' => "{$user->user_name}您好，您的订单{$order->no}退款{$order->total_amount}成功",
            'email' => $user->email,
            'isHtml' => true
        ];
        $emailQueueService->pushSendEmailJob($jobParams, 0);
    }
}
