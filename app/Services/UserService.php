<?php
/**
 * Created by PhpStorm.
 * User: 简美
 * Date: 2020/3/30
 * Time: 17:25
 */

namespace App\Services;


use App\Exception\UserServiceException;
use App\Facade\Redis;
use App\Model\User;
use Carbon\Carbon;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Utils\Str;
use Phper666\JwtAuth\Jwt;

class UserService
{
    /**
     * @Inject
     * @var Jwt
     */
    private $jwt;

    /**
     * @Inject()
     * @var SmsQueueService
     */
    private $smsQueueService;

    /**
     * @Inject()
     * @var EmailQueueService
     */
    private $emailQueueService;

    /**
     * 注册
     * @param $data *注册数据
     * @return User *注册成功用户实体
     */
    public function register(array $data)
    {
        $data['password'] = md5($data['password']);

        if (key_exists('phone', $data))
        {
            $code = Redis::get($data['phone']);

            if ($code != $data['code'])
            {
                throw new UserServiceException(422, '短信验证码错误');
            }
        }

        $user = new User();
        $user->fill($data);
        $user->save();

        if (array_key_exists('email', $data))
        {
            $data['status'] = 1;
            $user->status = 1;
            $user->save();
            $this->sendVerifyEmailToUser($user);
        }

        return $user;
    }

    /***
     * 更新用户信息
     * @param User $user
     * @param $data
     * @return bool
     */
    public function updateUserInfo(User $user, $data)
    {
        $user->fill($data);
        return $user->save();
    }

    /**
     * 发送激活邮件到用户邮箱
     * @param User $user
     */
    public function sendVerifyEmailToUser(User $user)
    {
        if ($user->status !== User::DISABLES)
        {
            throw new UserServiceException(403, '账号已经激活');
        }

        $token = Str::random(16);
        $subject = '验证邮件';
        $verifyRoute = env('HTTP_TYPE') . "://" . env('SERVER_HOST') . ":39002/email/identity?token={$token}&userId={$user->id}";
        $body = "亲爱的" . $user->user_name . "：<br/>感谢您在我站注册了新帐号。<br/>请点击链接激活您的帐号。<br/> 
    <a href='{$verifyRoute}' target= 
'_blank'>{$verifyRoute}</a><br/> 
    如果以上链接无法点击，请将它复制到你的浏览器地址栏中进入访问，该链接24小时内有效。";

        $jobParams = [
            'subject' => $subject,
            'body' => $body,
            'email' => $user->email,
            'isHtml' => true
        ];
        $this->emailQueueService->pushSendEmailJob($jobParams, 0);

        $key = 'userID.' . $user->id;
        Redis::set($key, $token, 300);
    }

    /**
     * 邮件验证
     * @param array $data
     */
    public function verifyEmail(array $data)
    {
        $token = $data['token'];
        $userId = (int)$data['userId'];

        $user = User::getFirstById($userId);
        if (!$user)
        {
            throw new UserServiceException(403, '用户不存在');
        }

        if ($user->status !== User::DISABLES)
        {
            throw new UserServiceException(403, '账号已经激活');
        }

        $key = 'userID.' . $user->id;
        $checkToken = Redis::get($key);

        if (!$checkToken)
        {
            throw new UserServiceException(422, '验证token错误');
        }

        if ($checkToken !== $token)
        {
            throw new UserServiceException(422, '验证token错误');
        }

        $user->status = 0;
        $user->email_verify_date = Carbon::now();
        $user->save();
        Redis::del($key);
    }

    /**
     * 用户登录
     * @param $loginData
     * @return array
     */
    public function login(array $loginData)
    {
        $hash = password_hash(md5($loginData['password']), PASSWORD_DEFAULT);

        if (key_exists('email', $loginData))
        {
            $email = $loginData['email'];
            $user = User::getFirstByWhere(['email' => $email]);
        }
        else
        {
            $phone = $loginData['phone'];
            $user = User::getFirstByWhere(['phone' => $phone]);
        }

        if (!$user)
        {
            throw new UserServiceException(403, '用户不存在');
        }

        if (!password_verify($user->password, $hash))
        {
            throw new UserServiceException(422, '密码错误');
        }

        if ($user->status === User::DISABLES)
        {
            throw new UserServiceException(403, '账号已经禁用');
        }

        $token = (string)$this->jwt->getToken([
            'id' => $user->id,
            'user_name' => $user->user_name
        ]);

        $tokenData = [
            'token' => $token,
            'expTime' => $this->jwt->getTTL()
        ];

        return $tokenData;
    }

    /**
     * 重置密码
     * @param array $userData
     */
    public function resetPassword(array $userData)
    {
        if (array_key_exists('phone', $userData))
        {
            $user = User::getFirstByWhere(['phone' => $userData['phone']]);
            $this->resetPasswordByUserPhone($user);
        }
        else
        {
            $user = User::getFirstByWhere(['email' => $userData['email']]);
            $this->resetPasswordByUserEmail($user);
        }
    }

    /**
     * 根据邮箱重置密码
     * @param User $user
     */
    private function resetPasswordByUserEmail(User $user)
    {
        $newPassword = $user->resetPassword();
        $jobParams = [
            'subject' => '重置密码成功',
            'body' => "重置密码成功，你的新密码是:{$newPassword},请勿泄露。",
            'email' => $user->email
        ];
        $this->emailQueueService->pushSendEmailJob($jobParams, 0);
    }

    /**
     * 根据电话重置密码
     * @param User $user
     */
    private function resetPasswordByUserPhone(User $user)
    {
        $newPassword = $user->resetPassword();
        $jobParams = [
            'phone' => $user->phone,
            'smsParams' => [
                'code' => $newPassword
            ],
        ];
        $this->smsQueueService->pushSendSmsJob($jobParams, 0);
    }
}