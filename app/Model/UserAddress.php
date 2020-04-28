<?php

declare (strict_types=1);

namespace App\Model;

/**
 * @property int $id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property int $user_id
 * @property string $province
 * @property string $city
 * @property string $district
 * @property string $address
 * @property int $zip
 * @property string $contact_name
 * @property string $contact_phone
 * @property string $last_used_at
 */
class UserAddress extends ModelBase implements ModelInterface
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user_addresses';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['province','city','district','address','zip','contact_name','contact_phone','user_id','last_used_at'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['id' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime', 'user_id' => 'integer', 'zip' => 'integer'];

    public function user()
    {
        $this->belongsTo(User::class);
    }

    public function getFullAddressAttribute()
    {
        return "{$this->province}{$this->city}{$this->district}{$this->address}";
    }
}