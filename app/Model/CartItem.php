<?php

declare (strict_types=1);

namespace App\Model;

/**
 * @property int $id
 * @property int $user_id
 * @property int $product_sku_id
 * @property int $amount
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class CartItem extends ModelBase
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'cart_items';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['amount'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['id' => 'integer', 'user_id' => 'integer', 'product_sku_id' => 'integer', 'amount' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];

    public function ProductSku()
    {
        return $this->belongsTo(ProductSku::class);
    }

    public function User()
    {
        return $this->belongsTo(User::class);
    }
}