<?php

declare(strict_types=1);

namespace App\Controller;

use App\Exception\ServiceException;
use App\Model\Order;
use App\Model\User;
use App\Request\ApplyRefundRequest;
use App\Request\CrowdfundingOrderRequest;
use App\Request\CrowdfundingRequest;
use App\Request\HandleRefundRequest;
use App\Request\OrderRequest;
use App\Request\ReviewRequest;
use App\Request\SeckillOrderRequest;
use App\Request\SendOutGoodRequest;
use App\Services\OrderService;
use Hyperf\Di\Annotation\Inject;

class OrderController extends BaseController
{
    /**
     * @Inject()
     * @var OrderService
     */
    private $service;

    public function index()
    {
        /** @var $user User */
        $user = $this->request->getAttribute('user');

        //JOIN写法
//        $data = Db::table('orders')
//            ->join('order_items', 'order_items.order_id', 'orders.id')
//            ->join('products', 'order_items.product_id', 'products.id')
//            ->join('product_skus', 'product_skus.product_id', 'products.id')
//            ->where('user_id', $user->id)
//            ->paginate();

        //with写法
        //在没有性能瓶颈的情况下with能发挥 Eloquent ORM 的优势，而JOIN不能，这里不存在这种情况一律用with
        $data = $this->getPaginateData($user->order()->with('items.product', 'items.productSku')
            ->orderByDesc('created_at')->paginate($this->getPageSize()));

        return $this->response->json(responseSuccess(200, '', $data));

    }

    public function store(OrderRequest $request)
    {
        $order = $this->service->createOrder($request->getAttribute('user'), $request->validated());

        return $this->response->json(responseSuccess(201, '', $order));
    }

    public function crowdfunding(CrowdfundingOrderRequest $request)
    {
        $order = $this->service->crowdfunding($request->getAttribute('user'), $request->validated());
        return $this->response->json(responseSuccess(201, '', $order));
    }

    public function seckill(SeckillOrderRequest $request)
    {
        $order = $this->service->seckill(authUser(), $request->validated());
        return $this->response->json(responseSuccess(201, '', $order));
    }

    public function orderList()
    {
        $data = $this->getPaginateData(Order::query()->with('items')->with('user')->orderBy('paid_at', 'DESC')->paginate($this->getPageSize()));

        return $this->response->json(responseSuccess(200, '', $data));
    }

    public function orderInfo()
    {
        $order = Order::with('items')->with('user')->where('id', $this->request->route('id'))->first();
        if (!$order)
        {
            throw new ServiceException(403, '订单不存在');
        }

        return $this->response->json(responseSuccess(200, '', $order));
    }

    public function sendOutGood(SendOutGoodRequest $request)
    {
        $order = Order::getFirstById($this->request->route('id'));
        if (!$order)
        {
            throw new ServiceException(403, '订单不存在');
        }
        $company = (string)$request->input('company');
        $express_no = (string)$request->input('express_no');
        $this->service->sendOutGood($order, $company, $express_no);
        return $this->response->json(responseSuccess(200, '发货成功'));
    }

    public function receivedGood()
    {
        $order = Order::getFirstById($this->request->route('order_id'));
        if (!$order)
        {
            throw new ServiceException(403, '订单不存在');
        }
        $this->service->receivedGood($order);
        return $this->response->json(responseSuccess(200, '收货成功'));
    }

    public function review(ReviewRequest $request)
    {
        $order = Order::getFirstById($this->request->route('order_id'));
        if (!$order)
        {
            throw new ServiceException(403, '订单不存在');
        }
        $reviews = $request->input('reviews');
        $this->service->review($order, $reviews);
        return $this->response->json(responseSuccess(200, '评论成功'));
    }

    public function applyRefund(ApplyRefundRequest $request)
    {
        $order = Order::getFirstById($this->request->route('order_id'));
        if (!$order)
        {
            throw new ServiceException(403, '订单不存在');
        }
        $reason = $request->input('reason');
        $this->service->applyRefund($order, $reason);
        return $this->response->json(responseSuccess(200, '申请退款成功'));
    }

    public function handleRefund(HandleRefundRequest $request)
    {
        $order = Order::getFirstById($this->request->route('id'));
        if (!$order)
        {
            throw new ServiceException(403, '订单不存在');
        }
        $agree = $request->input('agree');
        if ($agree)
        {
            //同意退款
            $this->service->refund($order);
        }
        else
        {
            //拒绝退款
            $this->service->refuseRefund($order, $request->input('reason'));
        }

        return $this->response->json(responseSuccess(200, '处理成功'));
    }
}
