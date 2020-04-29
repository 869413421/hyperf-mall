<?php

declare (strict_types=1);

namespace App\Model;


use Hyperf\Database\Model\Events\Deleting;
use Hyperf\DbConnection\Db;

class Permission extends \Donjan\Permission\Models\Permission
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'parent_id', 'url', 'name', 'display_name', 'guard_name', 'sort'
    ];

    public function deleting(Deleting $event)
    {
        Db::table('role_has_permissions')->where('permission_id', $this->id)->delete();
    }
}