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
    public static function createData()
    {
        \Hyperf\DbConnection\Db::table((new App\Model\Permission\Permission())->getTable())->insert([
            [
                'id' => 1,
                'parent_id' => 0,
                'url' => '/center',
                'name' => '系统管理',
                'display_name' => '系统管理',
                'guard_name' => 'web',
                'sort' => 0,
            ],
            [
                'id' => 2,
                'parent_id' => 1,
                'url' => '/center/admin/get',
                'name' => '用户管理',
                'display_name' => '用户管理',
                'guard_name' => 'web',
                'sort' => 0
            ],
            [
                'id' => 3,
                'parent_id' => 1,
                'url' => '/center/role/get',
                'name' => '角色管理',
                'display_name' => '角色管理',
                'guard_name' => 'web',
                'sort' => 0
            ],
            [
                'id' => 4,
                'parent_id' => 1,
                'url' => '/center/permission/get',
                'name' => '节点管理',
                'display_name' => '节点管理',
                'guard_name' => 'web',
                'sort' => 0
            ],
            [
                'id' => 5,
                'parent_id' => 2,
                'url' => '/center/admin/post',
                'name' => '新建用户',
                'display_name' => '新建用户',
                'guard_name' => 'web',
                'sort' => 0
            ], [
                'id' => 6,
                'parent_id' => 2,
                'url' => '/center/admin/patch',
                'name' => '编辑用户',
                'display_name' => '编辑用户',
                'guard_name' => 'web',
                'sort' => 0
            ],
            [
                'id' => 7,
                'parent_id' => 3,
                'url' => '/center/role/post',
                'name' => '新建角色',
                'display_name' => '新建角色',
                'guard_name' => 'web',
                'sort' => 0
            ], [
                'id' => 8,
                'parent_id' => 3,
                'url' => '/center/role/patch',
                'name' => '编辑角色',
                'display_name' => '编辑角色',
                'guard_name' => 'web',
                'sort' => 0
            ],
            [
                'id' => 9,
                'parent_id' => 3,
                'url' => '/center/role/delete',
                'name' => '删除角色',
                'display_name' => '删除角色',
                'guard_name' => 'web',
                'sort' => 0
            ],
            [
                'id' => 10,
                'parent_id' => 3,
                'url' => '/center/role/permission/patch',
                'name' => '为角色分配权限',
                'display_name' => '为角色分配权限',
                'guard_name' => 'web',
                'sort' => 0
            ],
            [
                'id' => 11,
                'parent_id' => 4,
                'url' => '/center/permission/post',
                'name' => '新建节点',
                'display_name' => '新建节点',
                'guard_name' => 'web',
                'sort' => 0
            ],
            [
                'id' => 12,
                'parent_id' => 4,
                'url' => '/center/permission/patch',
                'name' => '编辑节点',
                'display_name' => '编辑节点',
                'guard_name' => 'web',
                'sort' => 0
            ],
            [
                'id' => 13,
                'parent_id' => 4,
                'url' => '/center/permission/delete',
                'name' => '删除节点',
                'display_name' => '删除节点',
                'guard_name' => 'web',
                'sort' => 0
            ],
            [
                'id' => 14,
                'parent_id' => 4,
                'url' => '/center/admin/role/patch',
                'name' => '分配角色',
                'display_name' => '分配角色',
                'guard_name' => 'web',
                'sort' => 0
            ],
            [
                'id' => 15,
                'parent_id' => 2,
                'url' => '/center/admin/delete',
                'name' => '删除用户',
                'display_name' => '删除用户',
                'guard_name' => 'web',
                'sort' => 0
            ],
            [
                'id' => 16,
                'parent_id' => 2,
                'url' => '/center/admin/status/patch',
                'name' => '修改用户禁用状态',
                'display_name' => '修改用户禁用状态',
                'guard_name' => 'web',
                'sort' => 0
            ],
            [
                'id' => 17,
                'parent_id' => 2,
                'url' => '/center/admin/password/patch',
                'name' => '重置用户密码',
                'display_name' => '重置用户密码',
                'guard_name' => 'web',
                'sort' => 0
            ],
            [
                'id' => 18,
                'parent_id' => 2,
                'url' => '/center/admin/role/patch',
                'name' => '为用户分配角色',
                'display_name' => '为用户分配角色',
                'guard_name' => 'web',
                'sort' => 0
            ],
            [
                'id' => 19,
                'parent_id' => 1,
                'url' => '/center/product/get',
                'name' => '商品管理',
                'display_name' => '商品管理',
                'guard_name' => 'web',
                'sort' => 0
            ],
            [
                'id' => 20,
                'parent_id' => 19,
                'url' => '/center/product/post',
                'name' => '添加商品',
                'display_name' => '添加商品',
                'guard_name' => 'web',
                'sort' => 0
            ],
            [
                'id' => 21,
                'parent_id' => 19,
                'url' => '/center/product/patch',
                'name' => '更新商品',
                'display_name' => '更新商品',
                'guard_name' => 'web',
                'sort' => 0
            ],
            [
                'id' => 22,
                'parent_id' => 19,
                'url' => '/center/product/delete',
                'name' => '删除商品',
                'display_name' => '删除商品',
                'guard_name' => 'web',
                'sort' => 0
            ],
            [
                'id' => 23,
                'parent_id' => 19,
                'url' => '/center/product/sku/post',
                'name' => '添加商品库存',
                'display_name' => '添加商品库存',
                'guard_name' => 'web',
                'sort' => 0
            ],
            [
                'id' => 24,
                'parent_id' => 19,
                'url' => '/center/product/sku/patch',
                'name' => '更新商品',
                'display_name' => '更新商品',
                'guard_name' => 'web',
                'sort' => 0
            ],
            [
                'id' => 25,
                'parent_id' => 19,
                'url' => '/center/product/sku/delete',
                'name' => '删除商品',
                'display_name' => '删除商品',
                'guard_name' => 'web',
                'sort' => 0
            ]
        ]);
        $role = \App\Model\Permission\Role::create([
            'name' => '超级管理员',
            'guard_name' => 'web',
            'description' => '超级管理员'
        ]);
        $roleIdArr = \App\Model\Permission\Permission::query()->select('id')->pluck('id')->toArray();
        $role->permissions()->sync($roleIdArr);
        $user = \App\Model\User\User::where('id', 1)->first();
        $user->assignRole($role);
    }
}
