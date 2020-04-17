<?php

declare (strict_types=1);

namespace App\Model\User;

use App\Model\ModelBase;
use App\Model\ModelInterface;
use Donjan\Permission\Traits\HasRoles;
use Hyperf\Database\Model\Events\Deleted;
use Hyperf\DbConnection\Db;

/**
 * @property int $id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string $wx_user_id
 * @property string $user_name
 * @property string $password
 * @property string $email
 * @property string $phone
 * @property string $real_name
 * @property string $last_login_at
 * @property int $sex
 * @property string $avatar
 * @property string $remember_token
 * @property int $status
 * @property \Carbon\Carbon $email_verify_date
 */
class User extends ModelBase implements ModelInterface
{
    use HasRoles;

    const DISABLES = 1;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'users';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_name', 'password', 'email', 'phone', 'sex', 'real_name', 'avatar'
    ];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = ['id' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime', 'sex' => 'integer', 'status' => 'integer'];

    public function wxUser()
    {
        $this->belongsTo(WxUser::class);
    }

    public function addresses()
    {
        $this->hasMany(UserAddress::class);
    }

    public function resetPassword()
    {
        $this->password = md5('123456');
        $this->save();
    }

    public function changeDisablesStatus()
    {
        $this->status == self::DISABLES?$this->status=0:$this->status=self::DISABLES;
        $this->save();
    }

    public function deleted(Deleted $event)
    {
        Db::table('model_has_roles')->where('model_id', $this->id)->delete();
    }
}