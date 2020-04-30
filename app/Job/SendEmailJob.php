<?php

declare(strict_types=1);

namespace App\Job;

use App\Handler\Email\EmailMessageInterface;
use Hyperf\AsyncQueue\Job;

class SendEmailJob extends Job
{
    public $params;

    public function __construct(array $params)
    {
        $this->params = $params;
    }

    public function handle()
    {
        $handler = container()->get(EmailMessageInterface::class);
        $handler->subject($this->params['subject']);
        $handler->body($this->params['body']);
        $handler->address($this->params['email']);
        $handler->isHtml($this->params['isHtml']);
        $handler->send();
    }
}
