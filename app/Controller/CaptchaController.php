<?php

declare(strict_types=1);

namespace App\Controller;

use App\Request\CaptchaRequest;
use App\Utils\RedisUtil;
use Gregwar\Captcha\CaptchaBuilder;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Hyperf\Utils\Context;
use Psr\Http\Message\ResponseInterface;


class CaptchaController
{
    /**
     * @Inject
     * @var CaptchaBuilder
     */
    private $service;

    /**
     * @Inject()
     * @var RedisUtil
     */
    private $redis;

    public function show(CaptchaRequest $request)
    {
        $key = $request->input('sessionKey');
        $this->service->build();
        $code = $this->service->getPhrase();
        $this->redis->set($key, $code, 300);
        return $this->response()->withAddedHeader('content-type', 'image/jpg')
            ->withBody(new SwooleStream($this->service->get()));
    }

    protected function response(): ResponseInterface
    {
        return Context::get(ResponseInterface::class);
    }
}
