<?php

declare(strict_types=1);

namespace App\Controller;

use App\Exception\ServiceException;
use App\Model\Role;
use App\Model\User;
use App\Request\AdminRequest;

class AdminController extends BaseController
{
    public function index(AdminRequest $request)
    {
        $query = User::query()->with('roles');
        if ($user_name = $request->input('user_name'))
        {
            $query->where('user_name', 'like', "%$user_name%");
        }
        if ($role_id = $request->input('role_id'))
        {
            $query->whereHas('roles', function ($query) use ($role_id)
            {
                $query->where('id', $role_id);
            });
        }
        $status = $request->input('status');
        if ($status != null)
        {
            $query->where('status', $status);
        }
        if ($sort = $request->input('sort'))
        {
            $query->orderBy('id', $sort);
        }

        $data = $this->getPaginateData($query->paginate($this->getPageSize()));
        $data['roles'] = Role::query()->get()->toArray();
        return $this->response->json(responseSuccess(200, '', $data));
    }

    public function store(AdminRequest $request)
    {
        $data = $request->validated();
        $data['password'] = md5($data['password']);
        $user = User::query()->create($data);
        if ($role_id = $request->input('role_id'))
        {
            $user->roles()->sync($role_id);
        }
        $user = User::with('roles')->where('id', $user->id)->first();
        return $this->response->json(responseSuccess(201, '', $user));
    }

    public function update(AdminRequest $request)
    {
        $data = $request->validated();
        if (array_key_exists('password', $data))
        {
            $data['password'] = md5($data['password']);
        }

        $user = User::getFirstById($request->route('id'));
        if (!$user)
        {
            throw new ServiceException(403, '用户不存在');
        }
        $user->fill($data)->save();
        if ($role_id = $request->input('role_id'))
        {
            $user->roles()->sync($role_id);
        }
        $user = User::with('roles')->where('id', $user->id)->first();
        return $this->response->json(responseSuccess(200, '', $user));
    }

    public function delete(AdminRequest $request)
    {
        $user = User::getFirstById($request->route('id'));
        if (!$user)
        {
            throw new ServiceException(403, '用户不存在');
        }

        if ($user->id === 1)
        {
            throw new ServiceException(-403, '超级管理员不允许删除');
        }
        $user->delete();

        return $this->response->json(responseSuccess(200, '删除成功'));
    }

    public function AssigningRole()
    {
        $user = User::getFirstById($this->request->route('id'));
        if (!$user)
        {
            throw new ServiceException(403, '用户不存在');
        }
        $role = Role::findById((int)$this->request->route('role_id'));
        if (!$role)
        {
            throw new ServiceException(403, '角色不存在');
        }
        $user->assignRole($role);

        return $this->response->json(responseSuccess(200, '分配成功'));
    }

    public function resetPassword()
    {
        $user = User::getFirstById($this->request->route('id'));
        if (!$user)
        {
            throw new ServiceException(403, '用户不存在');
        }

        $password = $user->resetPassword();
        return $this->response->json(responseSuccess(200, '重置密码成功', [
            'password' => $password
        ]));
    }

    public function disable()
    {
        $user = User::getFirstById($this->request->route('id'));
        if (!$user)
        {
            throw new ServiceException(403, '用户不存在');
        }
        $user->changeDisablesStatus();
        return $this->response->json(responseSuccess(200, '修改成功'));
    }
}
