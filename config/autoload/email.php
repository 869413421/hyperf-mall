<?php

declare(strict_types=1);
//邮件配置
return [
    'default' => [
        'chartSet' => 'UTF-8',
        'host' => env('EMAIL_HOST'),
        'smtpAuth' => true,
        'form' => env('EMAIL'),
        'userName' => env('EMAIL_USER_NAME'),
        'passWord' => env('EMAIL_USER_PWD'),
        'smtpSecure' => 'ssl',
        'port' => env('EMAIL_PORT'),
    ]
];
