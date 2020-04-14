<?php

declare(strict_types=1);

use Hyperf\Database\Seeders\Seeder;

class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \Hyperf\DbConnection\Db::table((new App\Model\Permission\Permission())->getTable())->insert([
            [
                'id' => 1,
                'parent_id' => 0,
                'url' => 'center',
                'name' => '系统管理',
                'display_name' => '系统管理',
                'guard_name' => 'web',
                'sort' => 0,
            ],
            [
                'id' => 2,
                'parent_id' => 1,
                'url' => 'center/admin',
                'name' => 'center/admin/get',
                'display_name' => '用户管理',
                'guard_name' => 'web',
                'sort' => 0
            ],
            [
                'id' => 3,
                'parent_id' => 1,
                'url' => 'center/role',
                'name' => '/center/role/get',
                'display_name' => '角色管理',
                'guard_name' => 'web',
                'sort' => 0
            ],
            [
                'id' => 4,
                'parent_id' => 1,
                'url' => 'center/permissions',
                'name' => '/center/permissions/get',
                'display_name' => '节点管理',
                'guard_name' => 'web',
                'sort' => 0
            ],
            [
                'id' => 5,
                'parent_id' => 2,
                'url' => 'center/admin',
                'name' => '/center/admin/post',
                'display_name' => '新建用户',
                'guard_name' => 'web',
                'sort' => 0
            ], [
                'id' => 6,
                'parent_id' => 2,
                'url' => 'center/admin',
                'name' => '/center/admin/{id:\d+}/patch',
                'display_name' => '编辑用户',
                'guard_name' => 'web',
                'sort' => 0
            ],
            [
                'id' => 7,
                'parent_id' => 3,
                'url' => 'center/role',
                'name' => '/center/role/post',
                'display_name' => '新建角色',
                'guard_name' => 'web',
                'sort' => 0
            ], [
                'id' => 8,
                'parent_id' => 3,
                'url' => 'center/role',
                'name' => '/center/role/{id:\d+}/patch',
                'display_name' => '编辑角色',
                'guard_name' => 'web',
                'sort' => 0
            ],
            [
                'id' => 9,
                'parent_id' => 4,
                'url' => 'center/permissions',
                'name' => '/center/permission/post',
                'display_name' => '新建节点',
                'guard_name' => 'web',
                'sort' => 0
            ],
            [
                'id' => 10,
                'parent_id' => 4,
                'url' => 'center/permissions',
                'name' => '/center/permission/{id:\d+}/patch',
                'display_name' => '编辑节点',
                'guard_name' => 'web',
                'sort' => 0
            ],
            [
                'id' => 11,
                'parent_id' => 4,
                'url' => 'center/admin',
                'name' => '/center/admin/{id:\d+}/role/patch',
                'display_name' => '分配角色',
                'guard_name' => 'web',
                'sort' => 0
            ]
        ]);
        $role = \App\Model\Permission\Role::create([
            'name' => '超级管理员',
            'guard_name' => 'web',
            'description'=>'超级管理员'
        ]);
        $role->permissions()->sync([1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11]);
        $user = \App\Model\User\User::where('id', 1)->first();
        $user->assignRole($role);
    }
}
