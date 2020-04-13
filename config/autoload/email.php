<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://doc.hyperf.io
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf-cloud/hyperf/blob/master/LICENSE
 */

return [
    'default' => [
        'chartSet' => 'UTF-8',
        'host' => env('email_host', 'smtp.163.com'),
        'smtpAuth' => true,
        'form' => env('email', '13528685024@163.com'),
        'userName' => env('userName', '13528685024@163.com'),
        'passWord' => env('passWord', 'ZDSHOMZNATCYOQOS'),
        'smtpSecure' => 'ssl',
        'port' => 465,
    ]
];
