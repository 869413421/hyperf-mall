<?php

declare (strict_types=1);

namespace App\Model\Permission;


use Hyperf\Database\Model\Events\Deleted;
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

    public function deleted(Deleted $event)
    {
        Db::table('role_has_permissions')->where('permission_id', $this->id)->delete();
    }
}