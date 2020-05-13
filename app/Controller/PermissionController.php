<?php

declare(strict_types=1);

namespace App\Controller;

use App\Exception\ServiceException;
use App\Model\Permission;
use App\Request\PermissionRequest;

class PermissionController extends BaseController
{
    public function index()
    {
        $data = $this->getPaginateData(Permission::query()->paginate($this->getPageSize()));
        return $this->response->json(responseSuccess(200, '', $data));
    }

    public function store(PermissionRequest $request)
    {
        Permission::create($request->validated());
        return $this->response->json(responseSuccess(201));
    }

    public function update(PermissionRequest $request)
    {
        $data = $request->validated();
        $permission = Permission::query()->where('id', $request->route('id'))->first();
        if (!$permission)
        {
            throw new ServiceException(403, '权限不存在');
        }
        $permission->fill($data);
        $permission->save();
        return $this->response->json(responseSuccess(200, '更新成功'));
    }

    public function delete(PermissionRequest $request)
    {
        $id = $request->route('id');
        $permission = Permission::query()->where('id', $id)->first();
        if (!$permission)
        {
            throw new ServiceException(403, '权限不存在');
        }
        $permission->delete();
        return $this->response->json(responseSuccess(200, '删除成功'));
    }
}
