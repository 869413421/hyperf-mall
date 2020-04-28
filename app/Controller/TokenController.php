<?php

declare(strict_types=1);

namespace App\Controller;

use App\Request\TokenRequest;
use App\Services\UserService;
use Hyperf\Di\Annotation\Inject;
use Phper666\JwtAuth\Jwt;

class TokenController extends BaseController
{
    /**
     * @Inject()
     * @var UserService
     */
    private $userService;

    /**
     * @Inject()
     * @var Jwt
     */
    private $jwt;

    public function store(TokenRequest $request)
    {
        $loginData = $request->validated();
        $tokenData = $this->userService->login($loginData);

        return $this->response->json(responseSuccess(200, '登陆成功', $tokenData));
    }

    public function update()
    {
        $token = (string)$this->jwt->refreshToken();

        $tokenData = [
            'token' => $token,
            'expTime' => $this->jwt->getTTL()
        ];

        return $this->response->json(responseSuccess(200, '更新成功', $tokenData));
    }

    public function delete()
    {
        $this->jwt->logout();
        return $this->response->json(responseSuccess(200, '退出成功'));
    }
}
