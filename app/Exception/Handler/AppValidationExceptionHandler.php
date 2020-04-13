<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://doc.hyperf.io
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace App\Exception\Handler;

use App\Constants\ResponseCode;
use Hyperf\ExceptionHandler\ExceptionHandler;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Hyperf\Validation\ValidationException;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class AppValidationExceptionHandler extends ExceptionHandler
{
    public function handle(Throwable $throwable, ResponseInterface $response)
    {
        //阻止异常冒泡
        $this->stopPropagation();

        //返回自定义错误数据
        $result = responseError(ResponseCode::UNPROCESSABLE, '提交数据失败', $throwable->errors());

        return $response->withStatus(ResponseCode::UNPROCESSABLE)
            ->withAddedHeader('content-type', 'application/json')
            ->withBody(new SwooleStream(json_encode($result, JSON_UNESCAPED_UNICODE)));
    }

    /**
     *
     * @param Throwable $throwable 抛出的异常
     * @return bool 该异常处理器是否处理该异常
     */
    public function isValid(Throwable $throwable): bool
    {
        //当前的异常是否属于token验证异常
        return $throwable instanceof ValidationException;
    }
}
