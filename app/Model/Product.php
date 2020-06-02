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
        'title', 'long_title', 'description', 'image', 'on_sale', 'price'
    ];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['id' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime', 'on_sale' => 'boolean', 'rating' => 'float', 'sold_count' => 'integer', 'review_count' => 'integer', 'price' => 'float'];

    /**
     * 商品SKU
     * @return \Hyperf\Database\Model\Relations\HasMany
     */
    public function skus()
    {
        return $this->hasMany(ProductSku::class);
    }

    /**
     * 众筹商品
     * @return \Hyperf\Database\Model\Relations\HasOne
     */
    public function crowdfunding()
    {
        return $this->hasOne(CrowdfundingProduct::class);
    }

    /**
     * 商品分类
     * @return \Hyperf\Database\Model\Relations\BelongsTo
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * 商品属性
     * @return \Hyperf\Database\Model\Relations\HasMany
     */
    public function properties()
    {
        return $this->hasMany(ProductProperty::class);
    }

    /**
     * 返回一个新的商品属性数组
     * @return \Hyperf\Utils\Collection
     */
    public function getGroupedPropertiesAttribute()
    {
        return $this->properties
            // 按照属性名聚合，返回的集合的 key 是属性名，value 是包含该属性名的所有属性集合
            ->groupBy('name')
            ->map(function ($properties)
            {
                // 使用 map 方法将属性集合变为属性值集合
                return $properties->pluck('value')->all();
            });
    }

    public function deleted(Deleted $event)
    {
        Db::table('product_skus')->where('product_id', $this->id)->delete();
    }
}