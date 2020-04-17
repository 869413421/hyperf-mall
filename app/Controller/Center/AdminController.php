<?php

declare(strict_types=1);

namespace App\Controller\Center;

use App\Controller\BaseController;
use App\Exception\ServiceException;
use App\Model\Permission\Role;
use App\Model\User\User;
use App\Request\Center\Admin\AdminRequest;

class AdminController extends BaseController
{
    public function show()
    {
        $data = $this->getPaginateData(User::with('roles')->paginate());
        return $this->response->json(responseSuccess(200, '', $data));
    }

    public function store(AdminRequest $request)
    {
        $data = $request->validated();
        $data['password'] = md5($data['password']);
        User::query()->create($data);
        return $this->response->json(responseSuccess(201));
    }

    public function update(AdminRequest $request)
    {
        $data = $request->validated();
        if (array_key_exists('password', $data))
        {
            $data['password'] = md5($data['password']);
        }
        var_dump($data);
        User::getFirstById($request->input('id'))->fill($data)->save();
        return $this->response->json(responseSuccess(200, '更新成功'));
    }

    public function delete(AdminRequest $request)
    {
        $user = User::getFirstById($request->input('id'));
        if ($user->id === 1)
        {
            throw new ServiceException(-403, '超级管理员不允许删除');
        }
        $user->delete();

        return $this->response->json(responseSuccess(200, '删除成功'));
    }

    public function AssigningRole(AdminRequest $request)
    {
        $user = User::getFirstById($request->input('id'));
        $role = Role::findById((int)$request->input('role_id'));
        $user->assignRole($role);

        return $this->response->json(responseSuccess(200, '分配成功'));
    }

    public function resetPassword(AdminRequest $request)
    {
        User::getFirstById($request->input('id'))->resetPassword();
        return $this->response->json(responseSuccess(200, '重置密码成功'));
    }

    public function disable(AdminRequest $request)
    {
        User::getFirstById($request->input('id'))->changeDisablesStatus();
        return $this->response->json(responseSuccess(200, '修改成功'));
    }
}
