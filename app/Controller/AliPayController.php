<?php

declare(strict_types=1);

namespace App\Controller;

use App\Exception\ServiceException;
use App\Model\Installment;
use App\Request\AliPayWebRequest;
use App\Services\AliPayService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Hyperf\Utils\Context;
use Psr\Http\Message\ResponseInterface;

class AliPayController extends BaseController
{
    /**
     * @Inject()
     * @var AliPayService
     */
    private $service;

    public function store(AliPayWebRequest $request)
    {
        $content = $this->service->aliPayWeb($request->route('order_id'));
        return $this->response()->withAddedHeader('content-type', 'text/html')
            ->withBody(new SwooleStream($content));
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

    public function aliPayReturn()
    {
        $data = $this->service->aliPayWebReturn($this->request->all());
        return $this->response->json(responseSuccess(200, '', $data));
    }

    public function aliPayNotify()
    {
        $this->service->aliPayNotify($this->request->all());
    }

    public function installmentAliPayNotify()
    {
        $this->service->installmentAliPayNotify($this->request->all());
    }

    protected function response(): ResponseInterface
    {
        return Context::get(ResponseInterface::class);
    }
}
