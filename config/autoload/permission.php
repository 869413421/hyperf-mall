<?php

return [
    //model设置
    'models' => [
        'permission' => Donjan\Permission\Models\Permission::class,
        'role' => Donjan\Permission\Models\Role::class,
    ],
    //表名设置
    'table_names' => [
        'roles' => 'roles',
        'permissions' => 'permissions',
        'model_has_permissions' => 'model_has_permissions',
        'model_has_roles' => 'model_has_roles',
        'role_has_permissions' => 'role_has_permissions',
    ],
    'column_names' => [
        'model_morph_key' => 'model_id',//关联模板的主键
    ],
    'display_permission_in_exception' => false,
    'cache' => [
        'expiration_time' => 86400,
        'key' => 'donjan.permission.cache',
        'model_key' => 'name',
        'store' => 'default',
    ],
];
