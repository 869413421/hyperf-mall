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
use App\Model\Order;
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

        $used = Order::query()->where('user_id', authUser()->id)
            ->where('coupon_code_id', $couponCode->id)
            ->where(function ($query)
            {
                $query->where(function ($query)
                {
                    $query->whereNull('paid_at')
                        ->where('closed', false);
                })->orWhere(function ($query)
                {
                    $query->whereNotNull('paid_at')
                        ->where('refund_status', Order::REFUND_STATUS_PENDING);
                });
            })->exists();

        if ($used)
        {
            throw new ServiceException(403, '已经使用过该优惠券');
        }
        return $couponCode;
    }
}