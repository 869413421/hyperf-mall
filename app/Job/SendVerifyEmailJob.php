<?php

declare(strict_types=1);

namespace App\Job;

use App\Facade\Redis;
use App\Handler\Email\EmailMessageInterface;
use App\Model\User;
use Hyperf\AsyncQueue\Job;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Utils\Str;

class SendVerifyEmailJob extends Job
{
    public $user;

    /**
     * @Inject()
     * @var  EmailMessageInterface
     */
    private $emailHandler;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function handle()
    {
        $token = Str::random(16);
        $subject = '验证邮件';

        $verifyRoute = env('HTTP_TYPE') . "://" . env('SERVER_HOST') . "/email/identity?token={$token}&userId={$this->user->id}";
        $body = "亲爱的" . $this->user->user_name . "：<br/>感谢您在我站注册了新帐号。<br/>请点击链接激活您的帐号。<br/> 
    <a href='{$verifyRoute}' target= 
'_blank'>{$verifyRoute}</a><br/> 
    如果以上链接无法点击，请将它复制到你的浏览器地址栏中进入访问，该链接24小时内有效。";
        $this->emailHandler->subject($subject);
        $this->emailHandler->body($body);
        $this->emailHandler->address($this->user->email);
        $this->emailHandler->isHtml(true);
        $this->emailHandler->send();

        $key = 'userID.' . $this->user->id;
        Redis::set($key, $token);
    }
}
