<?php
/**
 * Created by PhpStorm.
 * User: 简美
 * Date: 2020/4/10
 * Time: 10:32
 */

namespace App\Services;


use App\Job\CloseOrderJob;
use App\Job\RefundInstallmentOrderJob;
use App\Model\Order;
use Hyperf\AsyncQueue\Driver\DriverFactory;
use Hyperf\AsyncQueue\Driver\DriverInterface;

class OrderQueueService
{
    /**
     * @var DriverInterface
     */
    private $driver;

    public function __construct(DriverFactory $driverFactory)
    {
        $this->driver = $driverFactory->get('default');
    }

    public function pushCloseOrderJod(Order $order, int $delay = 0): bool
    {
        return $this->driver->push(new CloseOrderJob($order), $delay);
    }

    public function pushRefundInstallmentOrderJob(Order $order, int $delay = 0): bool
    {
        return $this->driver->push(new RefundInstallmentOrderJob($order), $delay);
    }
}