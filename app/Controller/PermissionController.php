<?php

declare(strict_types=1);

namespace App\Controller;

use App\Exception\ServiceException;
use App\Model\Permission;
use App\Request\PermissionRequest;

class PermissionController extends BaseController
{
    public function index(PermissionRequest $request)
    {
        if ($this->getPageSize())
        {
            $query = Permission::query();
            if ($name = $request->input('name'))
            {
                $query->where('name', 'like', "$name");
            }
            $parent_id = $request->input('parent_id');
            if ($parent_id != null)
            {
                $query->where('parent_id', $parent_id);
            }
            if ($sort = $request->input('sort'))
            {
                $query->orderBy('id', $sort);
            }
            $data = $this->getPaginateData($query->paginate($this->getPageSize()));
            $data['all'] = Permission::query()->get()->toArray();
            return $this->response->json(responseSuccess(200, '', $data));
        }
        return $this->response->json(responseSuccess(200, '', Permission::getMenuList()));
    }

    public function store(PermissionRequest $request)
    {
        $permission = Permission::create($request->validated());
        return $this->response->json(responseSuccess(201, '', $permission));
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
