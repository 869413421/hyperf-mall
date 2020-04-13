<?php

declare(strict_types=1);

namespace App\Controller\User;

use App\Constants\ResponseCode;
use App\Controller\BaseController;
use App\Request\User\UserRequest;
use App\Services\User\UserService;
use Hyperf\Di\Annotation\Inject;


class UserController extends BaseController
{
    /**
     * @Inject
     * @var UserService
     */
    private $service;

    /**
     * 用户注册
     * @param UserRequest $request
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function store(UserRequest $request)
    {
        $userData = $request->validated();
        $user = $this->service->register($userData);

        $message = '注册成功！';
        if ($user->status == 1)
        {
            $message = "注册成功，请前往邮箱{$user->email}激活账号";
        }

        return $this->response->json(responseSuccess(ResponseCode::CREATE_ED, $message));
    }

    /**
     * 更新用户信息
     * @param UserRequest $request
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function update(UserRequest $request)
    {
        $user = $request->getAttribute('user');
        if (!$this->service->updateUserInfo($user, $request->validated()))
        {
            return $this->response->json(responseError(ResponseCode::ERROR, '更新失败'));
        }

        return $this->response->json(responseSuccess(ResponseCode::SUCCESS, '更新成功'));
    }


    public function resetPassword(UserRequest $request)
    {
        $this->service->resetPassword($request->validated());

        return $this->response->json(responseSuccess(ResponseCode::SUCCESS, '重置成功'));
    }

}
