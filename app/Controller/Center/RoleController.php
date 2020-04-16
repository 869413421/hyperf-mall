<?php

declare(strict_types=1);

namespace App\Controller\Center;

use App\Controller\BaseController;
use App\Exception\ServiceException;
use App\Request\Center\Role\RoleRequest;
use Donjan\Permission\Models\Role;

class RoleController extends BaseController
{
    public function show()
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
        $role = Role::query()->where('id', $data['id'])->first();
        $role->fill($data);
        $role->save();
        return $this->response->json(responseSuccess(200, '更新成功'));
    }

    public function delete(RoleRequest $request)
    {
        $id = $request->input('id');
        $role = Role::query()->where('id', $id)->first();
        if ($role->id === 1)
        {
            throw new ServiceException(403, '超级管理员角色不允许删除');
        }
        $role->delete();
        return $this->response->json(responseSuccess(200, '删除成功'));
    }

    public function assigningPermission(RoleRequest $request)
    {
        $id = (int)$request->input('id');
        $permissionIds = $request->input('ids');
        Role::findById($id)->permissions()->sync($permissionIds);
        return $this->response->json(responseSuccess(200, '分配成功'));
    }
}
