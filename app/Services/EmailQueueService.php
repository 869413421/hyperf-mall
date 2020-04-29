<?php
/**
 * Created by PhpStorm.
 * User: 简美
 * Date: 2020/4/10
 * Time: 10:32
 */

namespace App\Services;

use App\Job\SendVerifyEmailJob;
use App\Model\User;
use Hyperf\AsyncQueue\Driver\DriverFactory;
use Hyperf\AsyncQueue\Driver\DriverInterface;

class EmailQueueService
{
    /**
     * @var DriverInterface
     */
    private $driver;

    public function __construct(DriverFactory $driverFactory)
    {
        $this->driver = $driverFactory->get('default');
    }

    public function pushSendVerifyEmailJob(User $user, int $delay): bool
    {
        return $this->driver->push(new SendVerifyEmailJob($user), $delay);
    }
}