<?php

declare (strict_types=1);

namespace App\Model;


use Hyperf\Database\Model\Events\Deleting;

class Role extends \Donjan\Permission\Models\Role
{
    public function deleting(Deleting $event)
    {
        Db::table('model_has_roles')->where('role_id', $this->id)->delete();
    }
}