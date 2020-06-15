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
}