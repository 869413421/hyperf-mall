<?php

declare(strict_types=1);

namespace App\Listener;

use App\Event\SavedSeckillEvent;
use App\Facade\Redis;
use App\Model\Product;
use App\Model\ProductSku;
use App\Model\SeckillProduct;
use Hyperf\Database\Model\Events\Saved;
use Hyperf\Event\Annotation\Listener;
use Psr\Container\ContainerInterface;
use Hyperf\Event\Contract\ListenerInterface;

/**
 * @Listener(priority=10)
 */
class SavedSeckillListener implements ListenerInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function listen(): array
    {
        return [
            SavedSeckillEvent::class
        ];
    }

    /**
     * @param object $event
     */
    public function process(object $event)
    {
        var_dump('成功');
        /** @var $seckillProduct SeckillProduct */
        $seckillProduct = $event->seckillProduct;

        /** @var $product Product */
        $product = $seckillProduct->product;
        foreach ($product->skus as $productSku)
        {
            /** @var $productSku ProductSku */
            if ($product->on_sale && $product->type == Product::TYPE_SECKILL && !$seckillProduct->is_after_end)
            {
                $diff = $seckillProduct->end_at->getTimestamp() - time();;
                var_dump($diff);
                // 将剩余库存写入到 Redis 中，并设置该值过期时间为秒杀截止时间
                $result = Redis::setex('seckill_sku_' . $productSku->id, $diff, $productSku->stock);
                var_dump($result);
            }
            else
            {
                $result = Redis::del('seckill_sku_' . $productSku->id);
            }
        }
    }
}
