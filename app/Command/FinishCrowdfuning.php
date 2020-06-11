<?php

declare(strict_types=1);

namespace App\Command;

use App\Model\CrowdfundingProduct;
use App\Model\Order;
use App\Services\OrderService;
use Carbon\Carbon;
use Hyperf\Command\Command as HyperfCommand;
use Hyperf\Command\Annotation\Command;
use Hyperf\Utils\Exception\ParallelExecutionException;
use Hyperf\Utils\Parallel;
use Psr\Container\ContainerInterface;

/**
 * 众筹结束业务
 * @Command
 */
class FinishCrowdfuning extends HyperfCommand
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;

        parent::__construct('finish:crowdfunding');
    }

    public function configure()
    {
        parent::configure();
        $this->setDescription('结束众筹');
    }

    public function handle()
    {
        $this->line('finish crowdfunding start', 'info');
        CrowdfundingProduct::query()
            ->with('product')
            ->where('status', CrowdfundingProduct::STATUS_FUNDING)
            ->where('end_time', '<', Carbon::now())
            ->each(function (CrowdfundingProduct $crowdfunding)
            {
                if ($crowdfunding->total_amount >= $crowdfunding->target_amount)
                {
                    //众筹完成
                    $this->info('crowdfunding success');
                    $this->crowdfundingSuccess($crowdfunding);

                }
                else
                {
                    //众筹失败
                    $this->info('crowdfunding fail');
                    $this->crowdfundingFail($crowdfunding);
                }
            });
    }

    protected function crowdfundingSuccess(CrowdfundingProduct $crowdfunding)
    {
        $crowdfunding->update(['status' => CrowdfundingProduct::STATUS_SUCCESS]);
    }

    public function crowdfundingFail(CrowdfundingProduct $crowdfunding)
    {
        $orderService = $this->container->get(OrderService::class);
        $orderList = Order::query()->where('type', Order::TYPE_CROWDFUNDING)
            ->whereNotNull('paid_at')
            ->where('refund_status', Order::REFUND_STATUS_PENDING)
            ->whereHas('items', function ($query) use ($crowdfunding)
            {
                $query->where('product_id', $crowdfunding->product_id);
            })
            ->get();
        //因为使用第三方的组件不支持协程会阻塞，所以使用协程请求退款接口
        $parallel = new Parallel();
        foreach ($orderList as $order)
        {
            $parallel->add(function () use ($order, $orderService)
            {
                $this->info("order_no:" . $order->id);
                $order->refund_status = Order::REFUND_STATUS_APPLIED;
                $orderService->refund($order);
            });

        };

        try
        {
            $results = $parallel->wait();
        }
        catch (ParallelExecutionException $e)
        {
            foreach ($e->getThrowables() as $ex)
            {
                throw $ex;
            }
        }

        $crowdfunding->update(['status' => CrowdfundingProduct::STATUS_FAIL]);
    }
}
