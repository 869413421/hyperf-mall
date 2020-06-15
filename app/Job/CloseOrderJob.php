<?php

declare(strict_types=1);

namespace App\Job;

use App\Facade\Redis;
use App\Model\Order;
use App\Model\OrderItem;
use App\Model\ProductSku;
use Hyperf\AsyncQueue\Job;
use Hyperf\DbConnection\Db;

class CloseOrderJob extends Job
{
    public $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function handle()
    {
        if ($this->order->paid_at)
        {
            return;
        }

        Db::transaction(function ()
        {
            $this->order->closed = true;
            $this->order->save();

            /** @var $item OrderItem */
            foreach ($this->order->items as $item)
            {
                /**@var $productSku ProductSku * */
                $productSku = $item->productSku;
                if ($productSku->addStock($item->amount) <= 0)
                {
                    throw new \Exception('回复库存异常', 500);
                }
                //秒杀订单回复redis库存
                if ($item->order->type === Order::TYPE_SECKILL
                    && $item->product->on_sale
                    && !$item->product->seckill->is_after_end)
                {
                    // 将 Redis 中的库存 +1
                    Redis::incr('seckill_sku_' . $item->productSku->id);
                }
            }
        });
    }
}
