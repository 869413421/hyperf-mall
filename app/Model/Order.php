<?php

declare (strict_types=1);

namespace App\Model;

use Hyperf\Database\Model\Events\Creating;

/**
 * @property int $id
 * @property string $type
 * @property string $no
 * @property int $user_id
 * @property string $address
 * @property float $total_amount
 * @property string $remark
 * @property string $paid_at
 * @property string $payment_method
 * @property string $payment_no
 * @property string $refund_status
 * @property string $refund_no
 * @property int $closed
 * @property int $reviewed
 * @property string $ship_status
 * @property string $ship_data
 * @property string $extra
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class Order extends ModelBase implements ModelInterface
{
    const TYPE_NORMAL = 'normal';
    const TYPE_CROWDFUNDING = 'crowdfunding';
    const TYPE_SECKILL = 'seckill';

    public static $typeMap = [
        self::TYPE_NORMAL => '普通商品订单',
        self::TYPE_CROWDFUNDING => '众筹商品订单',
        self::TYPE_SECKILL => '秒杀商品订单',
    ];

    const REFUND_STATUS_PENDING = 'pending';
    const REFUND_STATUS_APPLIED = 'applied';
    const REFUND_STATUS_PROCESSING = 'processing';
    const REFUND_STATUS_SUCCESS = 'success';
    const REFUND_STATUS_FAILED = 'failed';

    const SHIP_STATUS_PENDING = 'pending';
    const SHIP_STATUS_DELIVERED = 'delivered';
    const SHIP_STATUS_RECEIVED = 'received';

    public static $refundStatusMap = [
        self::REFUND_STATUS_PENDING => '未退款',
        self::REFUND_STATUS_APPLIED => '已申请退款',
        self::REFUND_STATUS_PROCESSING => '退款中',
        self::REFUND_STATUS_SUCCESS => '退款成功',
        self::REFUND_STATUS_FAILED => '退款失败',
    ];

    public static $shipStatusMap = [
        self::SHIP_STATUS_PENDING => '未发货',
        self::SHIP_STATUS_DELIVERED => '已发货',
        self::SHIP_STATUS_RECEIVED => '已收货',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'orders';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'type',
        'no',
        'address',
        'total_amount',
        'remark',
        'paid_at',
        'payment_method',
        'payment_no',
        'refund_status',
        'refund_no',
        'closed',
        'reviewed',
        'ship_status',
        'ship_data',
        'extra',
    ];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['id' => 'integer', 'user_id' => 'integer', 'total_amount' => 'float', 'closed' => 'boolean', 'reviewed' => 'boolean', 'created_at' => 'datetime', 'updated_at' => 'datetime', 'address' => 'json', 'ship_data' => 'json', 'extra' => 'json',];

    protected $dates = [
        'paid_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function couponCode()
    {
        return $this->belongsTo(CouponCode::class);
    }

    public function creating(Creating $event)
    {
        if (!$this->no)
        {
            $this->no = getUUID('order');
        }
    }

    public function getCrowdfundingStatusAttribute()
    {
        $item = $this->items()->first();
        /** @var $item OrderItem * */
        return $item->product->crowdfunding->status;
    }
}