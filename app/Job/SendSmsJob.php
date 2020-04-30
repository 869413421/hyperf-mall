<?php

declare(strict_types=1);

namespace App\Job;

use App\Handler\Sms\SmsInterface;
use Hyperf\AsyncQueue\Job;

class SendSmsJob extends Job
{
    public $params;

    public function __construct(array $params)
    {
        // 这里最好是普通数据，不要使用携带 IO 的对象，比如 PDO 对象
        $this->params = $params;
    }

    public function handle()
    {
        $handler = container()->get(SmsInterface::class);
        $handler->send($this->params['phone'], $this->params['smsParams']);
    }
}
