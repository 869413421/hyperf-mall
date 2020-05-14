<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://doc.hyperf.io
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf-cloud/hyperf/blob/master/LICENSE
 */

namespace App\Controller;


use App\Exception\ServiceException;
use App\Model\CouponCode;
use App\Request\CouponCodeRequest;
use App\Services\CouponCodeService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Validation\ValidationException;
use Hyperf\Validation\ValidatorFactory;

class CouponCodeController extends BaseController
{

    /**
     * @Inject()
     * @var CouponCodeService
     */
    private $couponCodeService;
    /**
     * @Inject()
     * @var ValidatorFactory
     */
    private $validationFactory;

    public function index()
    {
        $data = $this->getPaginateData(CouponCode::getList([], $this->getPageSize(), ['*'], 'created_at', 'DESC'));
        return $this->response->json(responseSuccess(200, '', $data));
    }

    public function store(CouponCodeRequest $request)
    {
        CouponCode::query()->create($request->validated());
        return $this->response->json(responseSuccess(201));
    }

    public function show()
    {
        $couponCode = CouponCode::getFirstById($this->request->route('id'));
        if (!$couponCode)
        {
            throw new ServiceException(403, '优惠券不存在');
        }
        return $this->response->json(responseSuccess(200, '', $couponCode));
    }

    public function update(CouponCodeRequest $request)
    {
        $couponCode = CouponCode::getFirstById($request->route('id'));
        if (!$couponCode)
        {
            throw new ServiceException(403, '优惠券不存在');
        }
        $couponCode->update($request->validated());
        return $this->response->json(responseSuccess(200, '更新成功'));
    }

    public function delete()
    {
        $couponCode = CouponCode::getFirstById($this->request->route('id'));
        if (!$couponCode)
        {
            throw new ServiceException(403, '优惠券不存在');
        }
        $couponCode->delete();
        return $this->response->json(responseSuccess(200, '删除成功'));
    }

    public function couponCodeStatus()
    {
        $validator = $this->validationFactory->make($this->request->all(), [
            'code' => [
                'required'
            ]
        ], [

        ]);

        if ($validator->fails())
        {
            throw new ValidationException($validator);
        }
        $code = $this->request->input('code');
        $couponCode = $this->couponCodeService->checkCouponCode($code);

        return $this->response->json(responseSuccess(200, '', $couponCode));
    }
}
