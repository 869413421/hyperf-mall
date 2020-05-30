<?php

declare (strict_types=1);

namespace App\Model;

use Hyperf\Database\Model\Events\Deleting;

/**
 * @property int $id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property int $product_id
 * @property float $target_amount
 * @property float $total_amount
 * @property int $user_count
 * @property \Carbon\Carbon $end_time
 * @property string $status
 */
class CrowdfundingProduct extends ModelBase implements ModelInterface
{
    const STATUS_FAIL = 'fail';
    const STATUS_SUCCESS = 'success';
    const STATUS_FUNDING = 'funding';

    public static $statusMap = [
        self::STATUS_FAIL => '众筹失败',
        self::STATUS_SUCCESS => '众筹成功',
        self::STATUS_FUNDING => '众筹中',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'crowdfunding_products';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'target_amount', 'product_id', 'end_time', 'total_amount', 'user_count','status'
    ];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['id' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime', 'product_id' => 'integer', 'target_amount' => 'float', 'total_amount' => 'float', 'user_count' => 'integer', 'end_time' => 'datetime'];

    public function getPercentAttribute()
    {
        $value = $this->attributes['total_amount'] / $this->attributes['target_amount'];
        return floatval(number_format($value * 100, 2));
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
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