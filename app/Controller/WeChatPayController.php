<?php

declare(strict_types=1);

namespace App\Controller;

use App\Services\WeChatPayService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Psr\Http\Message\ResponseInterface;

class WeChatPayController extends BaseController
{
    /**
     * @Inject()
     * @var WeChatPayService
     */
    private $service;

    public function store()
    {
        $content = $this->service->weChatPayWeb($this->request->route('order_id'));
        return $this->response()->withAddedHeader('content-type', 'text/html')
            ->withBody(new SwooleStream($content));
    }

    public function aliPayNotify()
    {
        $this->service->weChatPayNotify($this->request->all());
    }

    protected function response(): ResponseInterface
    {
        return Context::get(ResponseInterface::class);
    }

}
