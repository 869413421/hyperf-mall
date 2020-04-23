<?php

declare (strict_types=1);

namespace App\Model\Order;

use App\Model\ModelBase;
use App\Model\ModelInterface;
use App\Model\User\User;
use Hyperf\Database\Model\Events\Creating;

/**
 * @property int $id
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

    public function creating(Creating $event)
    {
        if (!$this->no)
        {
            $this->no = getUUID('order');
        }
    }
}