<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\User;
use App\Request\EmailRequest;
use App\Services\UserService;
use Hyperf\Di\Annotation\Inject;

class EmailController extends BaseController
{
    /**
     * @Inject()
     * @var UserService
     */
    private $userService;

    public function sendVerifyEmail(EmailRequest $request)
    {
        $email = $request->input('email');
        $user = User::getFirstByWhere(['email' => $email]);

        if (!$user)
        {
            return $this->response->json(responseError(422, '邮箱没注册'));
        }

        $this->userService->sendVerifyEmailToUser($user);

        return $this->response->json(responseSuccess(201, '发送成功'));
    }

    /**
     * 验证用户激活邮件
     * @param EmailRequest $request
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function verifyEmail(EmailRequest $request)
    {
        $data = $request->validated();
        $this->userService->verifyEmail($data);

        return $this->response->json(responseSuccess(200));
    }
}
