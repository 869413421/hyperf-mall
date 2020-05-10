<?php

declare (strict_types=1);

namespace App\Model;

/**
 * @property int $id
 * @property int $order_id
 * @property int $product_id
 * @property int $product_sku_id
 * @property int $amount
 * @property float $price
 * @property int $rating
 * @property string $review
 * @property string $reviewed_at
 */
class OrderItem extends ModelBase implements ModelInterface
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'order_items';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['amount', 'price', 'rating', 'product_id', 'product_sku_id', 'review', 'reviewed_at'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['id' => 'integer', 'order_id' => 'integer', 'product_id' => 'integer', 'product_sku_id' => 'integer', 'amount' => 'integer', 'price' => 'float', 'rating' => 'integer'];

    public $timestamps = false;

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /***
     * @return \Hyperf\Database\Model\Relations\BelongsTo
     */
    public function productSku()
    {
        return $this->belongsTo(ProductSku::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}