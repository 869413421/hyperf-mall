<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\Permission;
use App\Request\PermissionRequest;

class PermissionController extends BaseController
{
    public function show()
    {
        $data = $this->getPaginateData(Permission::query()->paginate());
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
        $permission = Permission::query()->where('id', $data['id'])->first();
        $permission->fill($data);
        $permission->save();
        return $this->response->json(responseSuccess(200,'更新成功'));
    }

    public function delete(PermissionRequest $request)
    {
        $id = $request->input('id');
        $permission = Permission::query()->where('id', $id)->first();
        $permission->delete();
        return $this->response->json(responseSuccess(200,'删除成功'));
    }
}
