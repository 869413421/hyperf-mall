<?php
/**
 * Created by PhpStorm.
 * User: 简美
 * Date: 2020/4/10
 * Time: 10:32
 */

namespace App\Services;

use App\Job\SendSmsJob;
use Hyperf\AsyncQueue\Driver\DriverFactory;
use Hyperf\AsyncQueue\Driver\DriverInterface;

class SmsQueueService
{
    /**
     * @var DriverInterface
     */
    private $driver;

    public function __construct(DriverFactory $driverFactory)
    {
        $this->driver = $driverFactory->get('default');
    }

    public function pushSendSmsJob(array $params, int $delay = 0): bool
    {
        return $this->driver->push(new SendSmsJob($params), $delay);
    }
}