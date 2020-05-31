<?php

declare (strict_types=1);

namespace App\Model;


use Carbon\Carbon;

/**
 * @property int $id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property int $installment_id
 * @property int $sequence
 * @property float $base
 * @property float $fee
 * @property float $fine
 * @property string $due_data
 * @property string $paid_at
 * @property string $payment_method
 * @property string $payment_no
 * @property string $refund_status
 */
class InstallmentItem extends ModelBase implements ModelInterface
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'installment_items';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    const REFUND_STATUS_PENDING = 'pending';
    const REFUND_STATUS_PROCESSING = 'processing';
    const REFUND_STATUS_SUCCESS = 'success';
    const REFUND_STATUS_FAILED = 'failed';

    public static $refundStatusMap = [
        self::REFUND_STATUS_PENDING => '未退款',
        self::REFUND_STATUS_PROCESSING => '退款中',
        self::REFUND_STATUS_SUCCESS => '退款成功',
        self::REFUND_STATUS_FAILED => '退款失败',
    ];

    protected $fillable = [
        'sequence',
        'base',
        'fee',
        'fine',
        'due_date',
        'paid_at',
        'payment_method',
        'payment_no',
        'refund_status',
    ];
    protected $dates = ['due_date', 'paid_at'];

    public function installment()
    {
        return $this->belongsTo(Installment::class);
    }

    // 创建一个访问器，返回当前还款计划需还款的总金额
    public function getTotalAttribute()
    {
        $total = big_number($this->base)->add($this->fee);
        if (!is_null($this->fine))
        {
            $total->add($this->fine);
        }

        return $total->getValue();
    }

    // 创建一个访问器，返回当前还款计划是否已经逾期
    public function getIsOverdueAttribute()
    {
        return Carbon::now()->gt($this->due_date);
    }

    protected $casts = ['id' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime', 'installment_id' => 'integer', 'sequence' => 'integer', 'base' => 'float', 'fee' => 'float', 'fine' => 'float'];
}