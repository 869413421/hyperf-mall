<?php


namespace App\Services;

use App\Event\PaySuccessEvent;
use App\Exception\ServiceException;
use App\Model\Installment;
use App\Model\Order;
use Carbon\Carbon;
use Hyperf\DbConnection\Db;
use Hyperf\Di\Annotation\Inject;
use Psr\EventDispatcher\EventDispatcherInterface;

class InstallmentService
{
    /**
     * @Inject()
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;


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

    public function paid(array $data)
    {
        $paramsArr = explode('_', $data['out_trade_no']);
        $prefix = $paramsArr[0];
        $no = $paramsArr[1];
        $sequence = $paramsArr[2];
        /** @var $installment Installment */
        $installment = Installment::query()->where('no', $prefix . '_' . $no)->first();
        if (!$installment)
        {
            //DoSomeThing
            return false;
        }

        // 根据还款计划编号查询对应的还款计划，原则上不会找不到，这里的判断只是增强代码健壮性
        if (!$item = $installment->items()->where('sequence', $sequence)->first())
        {
            return false;
        }

        $item->update([
            'paid_at' => Carbon::now(), // 支付时间
            'payment_method' => 'alipay', // 支付方式
            'payment_no' => $data['trade_no'], // 支付宝订单号
        ]);

        // 如果这是第一笔还款
        if ($item->sequence === 0)
        {
            // 将分期付款的状态改为还款中
            $installment->update(['status' => Installment::STATUS_REPAYING]);
            // 将分期付款对应的商品订单状态改为已支付
            $installment->order->update([
                'paid_at' => Carbon::now(),
                'payment_method' => 'installment', // 支付方式为分期付款
                'payment_no' => $no, // 支付订单号为分期付款的流水号
            ]);
            // 触发商品订单已支付的事件
            $this->eventDispatcher->dispatch(new PaySuccessEvent($installment->order));
        }

        // 如果这是最后一笔还款
        if ($item->sequence === $installment->count - 1)
        {
            // 将分期付款状态改为已结清
            $installment->update(['status' => Installment::STATUS_FINISHED]);
        }

        return true;
    }
}