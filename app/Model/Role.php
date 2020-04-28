<?php

declare (strict_types=1);

namespace App\Model;


class Role extends \Donjan\Permission\Models\Role
{
    public function deleted(Deleted $event)
    {
        parent::deleted($event);
        Db::table('model_has_roles')->where('role_id', $this->id)->delete();
    }
}