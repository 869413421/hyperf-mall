<?php

declare (strict_types=1);

namespace App\Model;

use Carbon\Carbon;
use Hyperf\Database\Model\Events\Deleting;

/**
 * @property int $id
 * @property int $product_id
 * @property \Carbon\Carbon $start_at
 * @property \Carbon\Carbon $end_at
 */
class SeckillProduct extends ModelBase implements ModelInterface
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'seckill_products';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'start_at',
        'end_at',
        'product_id'
    ];

    protected $dates = [
        'start_at', 'end_at'
    ];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['id' => 'integer', 'product_id' => 'integer'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * 定义一个名为 is_before_start 的访问器，当前时间早于秒杀开始时间时返回 true
     * @return bool
     */
    public function getIsBeforeStartAttribute()
    {
        return Carbon::now()->lt($this->start_at);
    }

    /**
     * 定义一个名为 is_after_end 的访问器，当前时间晚于秒杀结束时间时返回 true
     * @return bool
     */
    public function getIsAfterEndAttribute()
    {
        return Carbon::now()->gt($this->end_at);
    }

    public function deleting(Deleting $event)
    {
        /** @var  $product  Product */
        $product = $this->product;
        foreach ($product->skus as $productSku)
        {
            /** @var $productSku ProductSku */
            $productSku->delete();
        }
        $product->delete();
    }
}