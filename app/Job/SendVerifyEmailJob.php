<?php

declare(strict_types=1);

namespace App\Job;

use App\Model\User;
use Hyperf\AsyncQueue\Job;

class SendVerifyEmailJob extends Job
{
    public $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function handle()
    {

    }
}
