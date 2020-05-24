<?php

declare(strict_types=1);

namespace App\Controller;

use App\Exception\ServiceException;
use App\Request\AssigningPermissionRequest;
use App\Request\RoleRequest;
use Donjan\Permission\Models\Role;

class RoleController extends BaseController
{
    public function index()
    {
        $data = $this->getPaginateData(Role::query()->with('permissions')->orderBy('created_at', 'DESC')->paginate());
        return $this->response->json(responseSuccess(200, '', $data));
    }

    public function store(RoleRequest $request)
    {
        $permissionsIds = $request->input('permissionsIds');
        $data = $request->validated();
        unset($data['permissionsIds']);
        $role = Role::create($data);

        $role->permissions()->sync($permissionsIds);
        $role=Role::query()->with('permissions')->where('id', $role->getKey())->first();
        return $this->response->json(responseSuccess(201, '', $role));
    }

    public function update(RoleRequest $request)
    {
        $data = $request->validated();
        /** @var $role Role */
        $role = Role::query()->where('id', $request->route('id'))->first();
        if (!$role)
        {
            throw new ServiceException(403, '角色不存在');
        }
        $permissionsIds = $request->input('permissionsIds');
        unset($data['permissionsIds']);
        $role->fill($data);
        $role->save();
        $role->permissions()->detach($role->permissions()->select('id')->get()->pluck('id'));
        $role->permissions()->sync($permissionsIds);

        return $this->response->json(responseSuccess(200, '更新成功'));
    }

    public function delete(RoleRequest $request)
    {
        $id = $request->route('id');
        $role = Role::query()->where('id', $id)->first();
        if (!$role)
        {
            throw new ServiceException(403, '角色不存在');
        }
        if ($role->id === 1)
        {
            throw new ServiceException(403, '超级管理员角色不允许删除');
        }
        $role->delete();
        return $this->response->json(responseSuccess(200, '删除成功'));
    }

    public function rolePermissions()
    {
        $id = $this->request->route('id');
        /** @var $role Role */
        $role = Role::query()->where('id', $id)->first();
        if (!$role)
        {
            throw new ServiceException(403, '角色不存在');
        }
        $permissions = $role->permissions;
        return $this->response->json(responseSuccess(200, '获取成功', $permissions));
    }

    public function assigningPermission(AssigningPermissionRequest $request)
    {
        $id = (int)$request->route('id');
        $permissionIds = $request->input('ids');
        $role = Role::findById($id)->permissions();
        if (!$role)
        {
            throw new ServiceException(403, '角色不存在');
        }
        $role->sync($permissionIds);
        return $this->response->json(responseSuccess(200, '分配成功'));
    }
}
