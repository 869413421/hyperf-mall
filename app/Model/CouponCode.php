<?php

declare (strict_types=1);

namespace App\Model;

use Hyperf\Database\Model\Events\Creating;
use Hyperf\Utils\Str;

/**
 * @property int $id
 * @property string $name
 * @property string $code
 * @property string $type
 * @property float $value
 * @property int $total
 * @property int $used
 * @property float $min_amount
 * @property \Carbon\Carbon $not_before
 * @property \Carbon\Carbon $not_after
 * @property int $enabled
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class CouponCode extends ModelBase implements ModelInterface
{
    // 用常量的方式定义支持的优惠券类型
    const TYPE_FIXED = 'fixed';
    const TYPE_PERCENT = 'percent';

    public static $typeMap = [
        self::TYPE_FIXED => '固定金额',
        self::TYPE_PERCENT => '比例',
    ];

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
        'min_amount',
        'enabled',
        'not_before',
        'not_after'
    ];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['id' => 'integer', 'value' => 'float', 'total' => 'integer', 'used' => 'integer', 'min_amount' => 'float', 'enabled' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime',];

    /**
     * 生成优惠券码
     * @param Creating $event
     */
    public function creating(Creating $event)
    {
        do
        {
            $code = strtoupper(Str::random(16));
        } while (self::query()->where('code', $code)->exists());
        $this->code = $code;
    }

    /***
     * 获取使用优惠券后订单金额
     * @param $orderAmount
     * @return float
     */
    public function getAdjustedPrice($orderAmount)
    {
        if ($this->type === self::TYPE_FIXED)
        {
            return max(0.01, $orderAmount - $this->value);
        }
        return number_format($orderAmount * (100 - $this->value) / 100, 2, '.', '');
    }

    public function changeUsed($increase = true)
    {
        if ($increase)
        {
            return $this->newQuery()->where('id', $this->id)->where('used', '<', $this->total)->increment('used');
        }

        return $this->decrement('used');
    }
}