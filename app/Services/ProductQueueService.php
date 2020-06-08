<?php
/**
 * Created by PhpStorm.
 * User: 简美
 * Date: 2020/6/8
 * Time: 11:05
 */

namespace App\Services;


use App\Job\SyncProductJob;
use App\Model\Product;
use Hyperf\AsyncQueue\Driver\DriverFactory;
use Hyperf\AsyncQueue\Driver\DriverInterface;

class ProductQueueService
{
    /**
     * @var DriverInterface
     */
    private $driver;

    public function __construct(DriverFactory $driverFactory)
    {
        $this->driver = $driverFactory->get('default');
    }

    public function pushSyncProductJob(Product $product, int $delay = 0): bool
    {
        return $this->driver->push(new SyncProductJob($product), $delay);
    }

}