<?php

declare(strict_types=1);

namespace App\Controller;

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

    }

    public function store(OrderRequest $request)
    {
        $order = $this->service->createOrder($request->getAttribute('user'), $request->validated());

        return $this->response->json(responseSuccess(201, '', $order));
    }
}
