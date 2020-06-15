<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Exception\ServiceException;
use Hyperf\Di\Annotation\Inject;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Hyperf\Contract\ConfigInterface;

class RandomDropSeckillMiddleware implements MiddlewareInterface
{

    /**
     * @Inject
     * @var ConfigInterface
     */
    protected $config;

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        //随机拒绝
        if (rand(0, 100) < 10)
        {
            throw new ServiceException(403, '人数过多请重试');
        }

        return $handler->handle($request);
    }

}
