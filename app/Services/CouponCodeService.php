<?php
/**
 * Created by PhpStorm.
 * User: 简美
 * Date: 2020/5/14
 * Time: 16:08
 */

namespace App\Services;


use App\Exception\ServiceException;
use App\Model\CouponCode;
use Carbon\Carbon;

class CouponCodeService
{
    /**
     * 检查优惠券
     * @param string $code *优惠券码
     * @return CouponCode
     */
    public function checkCouponCode(string $code)
    {
        $couponCode = CouponCode::getFirstByWhere(['code' => $code]);
        if (!$couponCode)
        {
            throw new ServiceException(404, '找不到优惠券');
        }
        if (!$couponCode->enabled)
        {
            throw new ServiceException(403, '优惠券暂没开启');
        }
        if ($couponCode->total - $couponCode->used <= 0)
        {
            throw new ServiceException(403, '优惠券已经兑换完');
        }
        if ($couponCode->not_before && Carbon::createFromTimeString($couponCode->not_before) > Carbon::now())
        {
            throw new ServiceException(403, '优惠券还没到使用时间');
        }
        if ($couponCode->not_after && Carbon::createFromTimeString($couponCode->not_after) < Carbon::now())
        {
            throw new ServiceException(403, '优惠券已经过期');
        }
        return $couponCode;
    }
}