<?php

declare(strict_types=1);

namespace App\Controller;

use App\Exception\ServiceException;
use App\Model\Installment;
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

    public function weChatPayNotify()
    {
        $this->service->weChatPayNotify($this->request->all());
    }

    public function installmentPay()
    {
        $installment = Installment::getFirstById($this->request->route('id'));
        if (!$installment)
        {
            throw new ServiceException(403, '订单不存在');
        }
        $content = $this->service->installmentPay($installment);
        return $this->response()->withAddedHeader('content-type', 'text/html')
            ->withBody(new SwooleStream($content));
    }

    public function installmentAliPayNotify()
    {
        $this->service->installmentAliPayNotify($this->request->all());
    }

    public function refundNotify()
    {
        $this->service->refundNotify($this->request->all());
    }

    public function installmentRefundNotify()
    {
        $this->service->refundNotify($this->request->all());
    }

    protected function response(): ResponseInterface
    {
        return Context::get(ResponseInterface::class);
    }

}
