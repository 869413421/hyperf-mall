<?php

declare(strict_types=1);

namespace App\Controller;


use App\Handler\Pay\PayFactory;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Hyperf\Utils\Context;
use Psr\Http\Message\ResponseInterface;

class PayController extends BaseController
{
    /**
     * @Inject()
     * @var PayFactory
     */
    private $payFactory;

    public function index()
    {
        $pay = $this->payFactory->get('alipay');
        return $this->response()->withAddedHeader('content-type', 'text/html')
            ->withBody(new SwooleStream($pay->webPay(getUUID('order'), 1, '测试支付')->getContent()));
    }

    protected function response(): ResponseInterface
    {
        return Context::get(ResponseInterface::class);
    }
}
