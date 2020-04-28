<?php

declare(strict_types=1);

namespace App\Controller;


use App\Model\User;
use App\Request\CartRequest;
use App\Services\CartService;
use Hyperf\Di\Annotation\Inject;

class CartController extends BaseController
{
    /**
     * @Inject()
     * @var CartService
     */
    private $service;

    public function index()
    {
        /**@var $user User* */
        $user = $this->request->getAttribute('user');
        $data = $user->cartItems()->with('productSku.product')->orderBy('created_at')->get();

        return $this->response->json(responseSuccess(200, '', $data));

    }

    public function store(CartRequest $request)
    {
        $this->service->add($request->getAttribute('user'), $request->input('sku_id'), $request->input('amount'));

        return $this->response->json(responseSuccess(201));
    }

    public function delete(CartRequest $request)
    {
        /** @var $user User */
        $user = $request->getAttribute('user');
        $user->cartItems()->where('product_sku_id', $request->input('sku_id'))->delete();

        return $this->response->json(responseSuccess(200));
    }
}
