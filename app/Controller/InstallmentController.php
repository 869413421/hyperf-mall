<?php

declare(strict_types=1);

namespace App\Controller;


use App\Exception\ServiceException;
use App\Model\Installment;
use App\Model\Order;
use App\Request\InstallmentRequest;
use App\Services\InstallmentService;
use Hyperf\Di\Annotation\Inject;

class InstallmentController extends BaseController
{
    /**
     * @Inject()
     * @var InstallmentService
     */
    private $installmentService;

    public function installment(InstallmentRequest $request)
    {
        $order = Order::getFirstById($request->route('order_id'));
        if (!$order)
        {
            throw new ServiceException(403, '订单不存在');
        }
        $count = $this->request->input('count');
        $installment = $this->installmentService->installment($order, $count);
        return $this->response->json(responseSuccess(201, '', $installment));
    }

    public function index()
    {
        $data = $this->getPaginateData(Installment::with(['user', 'order'])
            ->where('user_id', authUser()->id)
            ->orderBy('created_at')
            ->paginate($this->getPageSize()));

        return $this->response->json(responseSuccess(200, '', $data));
    }

    public function show()
    {
        $installment = Installment::query()->with('user', 'items', 'order')
            ->where('user_id', authUser()->id)
            ->where('id', $this->request->route('id'))
            ->first();
        if (!$installment)
        {
            throw new ServiceException(403, '订单不存在');
        }
        return $this->response->json(responseSuccess(200, '', $installment));
    }
}
