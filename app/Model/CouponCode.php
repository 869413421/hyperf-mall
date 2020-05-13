<?php

declare (strict_types=1);

namespace App\Model;

/**
 * @property int $id
 * @property string $name
 * @property string $code
 * @property string $type
 * @property float $value
 * @property int $total
 * @property int $used
 * @property float $min_amount
 * @property string $not_before
 * @property string $not_after
 * @property int $enabled
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class CouponCode extends ModelBase implements ModelInterface
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'coupon_codes';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'value',
        'type',
        'total',
        'used',
        'min_amount'
    ];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['id' => 'integer', 'value' => 'float', 'total' => 'integer', 'used' => 'integer', 'min_amount' => 'float', 'enabled' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];
}