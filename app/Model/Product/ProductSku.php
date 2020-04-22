<?php

declare (strict_types=1);

namespace App\Model\Product;

use App\Model\ModelBase;
use App\Model\ModelInterface;

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
}