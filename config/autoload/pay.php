<?php
declare(strict_types=1);

return [
    'alipay' => [
        'app_id' => env('ALI_PAY_APP_ID'),
        'ali_public_key' => env('ALI_PUBLIC_KEY'),
        'driver' => \App\Handler\Pay\AliPay::class,
        'private_key' => env('ALI_PRIVATE_KEY'),
        'log' => [
            'file' => '/hyperf-skeleton/runtime/logs/alipay.log',
        ],
        'mode' => 'dev',
        'notify_url' => 'http://'.env('SERVER_HOST').':39002/ali/pay/web/service',
        'return_url' => 'http://'.env('SERVER_HOST').':39002/ali/pay/web',
    ],

    'wechat' => [
        'app_id' => '',
        'mch_id' => '',
        'driver' => \App\Handler\Pay\WeChatPay::class,
        'key' => '',
        'cert_client' => '',
        'cert_key' => '',
        'log' => [
            'file' => '/hyperf-skeleton/runtime/logs/wechat_pay.log',
        ],
    ],
];