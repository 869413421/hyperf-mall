<?php

declare (strict_types=1);

namespace App\Model;

use App\Exception\ServiceException;
use App\Facade\Redis;
use Hyperf\Database\Model\Events\Saved;

/**
 * @property int $id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string $title
 * @property string $description
 * @property float $price
 * @property int $stock
 * @property int $product_id
 */
class ProductSku extends ModelBase implements ModelInterface
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'product_skus';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title', 'description', 'price', 'stock', 'product_id'
    ];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['id' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime', 'price' => 'float', 'stock' => 'integer', 'product_id' => 'integer'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function decreaseStock(int $amount): int
    {
        if ($amount <= 0)
        {
            throw new ServiceException(403, '库存不能减少0');
        }

        return $this->newQuery()->where('id', $this->id)->where('stock', '>=', $amount)->decrement('stock', $amount);
    }

    public function addStock(int $amount): int
    {
        if ($amount <= 0)
        {
            throw new ServiceException(403, '库存不能增加0');
        }
        var_dump($amount);
        return $this->newQuery()->where('id', $this->id)->increment('stock', $amount);
    }

    /**
     * 如果当前是秒杀商品，将库存存入redis中
     * @param Saved $event
     */
    public function saved(Saved $event)
    {
        var_dump('SKU触发');
        var_dump($this->product->on_sale);
        var_dump($this->product);
        var_dump($this->product->type);
        var_dump($this->product->type == Product::TYPE_SECKILL);
        var_dump($this->product->seckill->is_after_end);
        if ($this->product->on_sale && $this->product->type == Product::TYPE_SECKILL && !$this->product->seckill->is_after_end)
        {
            var_dump('成功');
            $diff = $this->product->seckill->end_at->getTimestamp() - time();;
            // 将剩余库存写入到 Redis 中，并设置该值过期时间为秒杀截止时间
            Redis::setex('seckill_sku_' . $this->id, $diff, $this->stock);
        }
        else
        {
            var_dump('失败');
            // 否则将该 SKU 的库存值从 Redis 中删除
            Redis::del('seckill_sku_' . $this->id);
        }
    }
}