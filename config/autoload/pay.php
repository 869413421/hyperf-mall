<?php
declare(strict_types=1);

return [
    'alipay' => [
        'app_id' => env('ALI_PAY_APP_ID'),
        'ali_public_key' => env('ALI_PUBLIC_KEY'),
        'driver' => \App\Handler\Pay\AliPay::class,
        'private_key' => env('ALI_PRIVATE_KEY'),
        'log' => [
            'file' => BASE_PATH . '/runtime/logs/alipay.log',
        ],
        'mode' => 'dev',
        'notify_url' => 'http://' . env('SERVER_HOST') . ':39002/ali/pay/web/service',
        'return_url' => 'http://' . env('SERVER_HOST') . ':39002/ali/pay/web',
    ],

    'wechat' => [
        'app_id' => env('WE_CHAT_PAY_APP_ID'),
        'mch_id' => env('WE_CHAT_PAY_MCH_ID'),
        'driver' => \App\Handler\Pay\WeChatPay::class,
        'key' => env('WE_CHAT_PAY_API_KEY'),
        'notify_url' => 'http://' . env('SERVER_HOST') . ':39002/wechat/pay/web/service',
        'cert_client' => BASE_PATH . '/resources/wechat_pay/apiclient_cert.pem',
        'cert_key' => BASE_PATH . '/resources/wechat_pay/apiclient_key.pem',
        'log' => [
            'file' => BASE_PATH . '/runtime/logs/wechat_pay.log',
        ],
    ],
];