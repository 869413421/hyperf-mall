<?php


namespace App\Services;

use App\Exception\ServiceException;
use App\Model\Installment;
use App\Model\Order;
use Carbon\Carbon;
use Hyperf\DbConnection\Db;

class InstallmentService
{
    /**
     * 创建分期订单
     * @param Order $order *订单
     * @param $count *分期数目
     * @return Installment
     */
    public function installment(Order $order, $count): Installment
    {
        if ($order->user_id !== authUser()->id)
        {
            throw new ServiceException(403, '无权操作此订单');
        }
        if ($order->paid_at || $order->closed)
        {
            throw new ServiceException(403, '订单已经付款或已关闭');
        }
        if ($order->total_amount < config('min_installment_amount'))
        {
            throw new ServiceException(403, '订单低于分期最低金额');
        }

        $installment = Db::transaction(function () use ($order, $count)
        {
            // 删除同一笔商品订单发起过其他的状态是未支付的分期付款，避免同一笔商品订单有多个分期付款
            $installmentList = Installment::query()
                ->where('order_id', $order->id)
                ->where('status', Installment::STATUS_PENDING)
                ->get();
            foreach ($installmentList as $item)
            {
                $item->delete();
            }
            // 创建一个新的分期付款对象
            $installment = new Installment([
                // 总本金即为商品订单总金额
                'total_amount' => $order->total_amount,
                // 分期期数
                'count' => $count,
                // 从配置文件中读取相应期数的费率
                'fee_rate' => config('installment_fee_rate')[$count],
                // 从配置文件中读取当期逾期费率
                'fine_rate' => config('installment_fine_rate'),
            ]);
            $installment->user()->associate($order->user);
            $installment->order()->associate($order);
            $installment->save();
            // 第一期的还款截止日期为明天凌晨 0 点
            $dueDate = Carbon::tomorrow();
            // 计算每一期的本金
            $base = big_number($order->total_amount)->divide($count)->getValue();
            // 计算每一期的手续费
            $fee = big_number($base)->multiply($installment->fee_rate)->divide(100)->getValue();
            // 根据用户选择的还款期数，创建对应数量的还款计划
            for ($i = 0; $i < $count; $i++)
            {
                // 最后一期的本金需要用总本金减去前面几期的本金
                if ($i === $count - 1)
                {
                    $base = big_number($order->total_amount)->subtract(big_number($base)->multiply($count - 1));
                }
                $installment->items()->create([
                    'sequence' => $i,
                    'base' => $base,
                    'fee' => $fee,
                    'due_date' => $dueDate,
                ]);
                // 还款截止日期加 30 天
                $dueDate = $dueDate->copy()->addDays(30);
            }

            return $installment;
        });

        return $installment;
    }
}