<?php

declare (strict_types=1);

namespace App\Model;

use Hyperf\Database\Model\Events\Creating;
use Hyperf\Database\Model\Events\Deleted;
use Hyperf\Database\Model\Events\Deleting;

/**
 * @property int $id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string $no
 * @property int $user_id
 * @property int $order_id
 * @property float $total_amount
 * @property int $count
 * @property float $fee_rate
 * @property float $fine_rate
 * @property string $status
 * @property-read \App\Model\User $user
 * @property-read \App\Model\Order $order
 * @property-read Hyperf\Database\Model\Collection|\App\Model\InstallmentItem[] $items
 */
class Installment extends ModelBase implements ModelInterface
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'installments';

    const STATUS_PENDING = 'pending';
    const STATUS_REPAYING = 'repaying';
    const STATUS_FINISHED = 'finished';

    public static $statusMap = [
        self::STATUS_PENDING => '未执行',
        self::STATUS_REPAYING => '还款中',
        self::STATUS_FINISHED => '已完成',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'no', 'total_amount', 'count', 'fee_rate', 'fine_rate', 'status'
    ];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['id' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime', 'user_id' => 'integer', 'order_id' => 'integer', 'total_amount' => 'float', 'count' => 'integer', 'fee_rate' => 'float', 'fine_rate' => 'float'];

    public function creating(Creating $event)
    {
        if (!$this->no)
        {
            $this->no = getUUID('installments');
        }
    }

    public function deleting(Deleting $event)
    {
        InstallmentItem::query()->where('installment_id', $this->id)->delete();
    }


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function items()
    {
        return $this->hasMany(InstallmentItem::class);
    }
}