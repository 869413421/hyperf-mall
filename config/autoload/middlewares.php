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
    'http' => [
        \App\Middleware\CorsMiddleware::class,
        //验证组件，必须配置
        \Hyperf\Validation\Middleware\ValidationMiddleware::class,

    ],
];
