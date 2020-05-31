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

use Hyperf\Contract\StdoutLoggerInterface;
use Psr\Log\LogLevel;

return [
    'app_name' => env('APP_NAME', 'skeleton'),
    StdoutLoggerInterface::class => [
        'log_level' => [
            LogLevel::ALERT,
            LogLevel::CRITICAL,
            LogLevel::DEBUG,
            LogLevel::EMERGENCY,
            LogLevel::ERROR,
            LogLevel::INFO,
            LogLevel::NOTICE,
            LogLevel::WARNING,
        ],
    ],
    'storage_path' => '/hyperf-skeleton/storage/upload/',
    'host' => 'http://47.94.155.227:39002/',
    //分期配置
    'installment_fee_rate' => [
        3 => 1.5,
        6 => 2,
        12 => 2.5,
    ], // 分期费率，key 为期数，value 为费率
    'min_installment_amount' => 300, // 最低分期金额
    'installment_fine_rate' => 0.05, // 逾期日息 0.05%
];
