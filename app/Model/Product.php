<?php

declare (strict_types=1);

namespace App\Model;

use Hyperf\Database\Model\Events\Deleted;
use Hyperf\DbConnection\Db;

/**
 * @property int $id
 * @property string $type
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string $title
 * @property string $description
 * @property string $image
 * @property int $on_sale
 * @property float $rating
 * @property int $sold_count
 * @property int $review_count
 * @property float $price
 */
class Product extends ModelBase implements ModelInterface
{
    const TYPE_NORMAL = 'normal';
    const TYPE_CROWDFUNDING = 'crowdfunding';
    public static $typeMap = [
        self::TYPE_NORMAL => '普通商品',
        self::TYPE_CROWDFUNDING => '众筹商品',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'products';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title', 'description', 'image', 'on_sale', 'price'
    ];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['id' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime', 'on_sale' => 'boolean', 'rating' => 'float', 'sold_count' => 'integer', 'review_count' => 'integer', 'price' => 'float'];

    public function skus()
    {
        return $this->hasMany(ProductSku::class);
    }

    public function crowdfunding()
    {
        return $this->hasOne(CrowdfundingProduct::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function deleted(Deleted $event)
    {
        Db::table('product_skus')->where('product_id', $this->id)->delete();
    }
}