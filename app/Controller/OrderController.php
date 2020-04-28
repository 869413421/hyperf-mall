<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\User;
use App\Request\OrderRequest;
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
            ->orderByDesc('created_at')->paginate());

        return $this->response->json(responseSuccess(200, '', $data));

    }

    public function store(OrderRequest $request)
    {
        $order = $this->service->createOrder($request->getAttribute('user'), $request->validated());

        return $this->response->json(responseSuccess(201, '', $order));
    }
}
