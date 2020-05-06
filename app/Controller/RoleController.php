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
        Role::create($request->validated());
        return $this->response->json(responseSuccess(201));
    }

    public function update(RoleRequest $request)
    {
        $data = $request->validated();
        $role = Role::query()->where('id', $request->route('id'))->first();
        if (!$role)
        {
            throw new ServiceException(403, '角色不存在');
        }
        $role->fill($data);
        $role->save();
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
