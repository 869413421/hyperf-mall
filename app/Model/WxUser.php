<?php

declare (strict_types=1);

namespace App\Model;

/**
 * @property int $id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property int $user_id
 * @property string $nick_name
 * @property string $avatar
 * @property string $open_id
 * @property string $union_id
 * @property string $access_token
 * @property string $access_token_expire_time
 * @property string $refresh_token
 * @property string $refresh_token_expire_time
 */
class WxUser extends ModelBase implements ModelInterface
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'wx_users';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['id' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime', 'user_id' => 'integer'];

    public function user()
    {
        $this->belongsTo(User::class);
    }
}