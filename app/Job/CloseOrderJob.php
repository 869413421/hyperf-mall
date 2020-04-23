<?php

declare(strict_types=1);

namespace App\Job;

use App\Model\Order\Order;
use App\Model\Order\OrderItem;
use App\Model\Product\ProductSku;
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
            }
        });
    }
}
