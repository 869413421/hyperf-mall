<?php

declare(strict_types=1);

namespace App\Controller\Sms;

use App\Controller\BaseController;
use App\Handler\Sms\SmsInterface;
use App\Request\Sms\SmsRequest;
use App\Utils\RedisUtil;
use Hyperf\Di\Annotation\Inject;

class SmsController extends BaseController
{
    /**
     * @Inject
     * @var SmsInterface
     */
    private $service;

    /**
     * @Inject()
     * @var RedisUtil
     */
    private $redis;

    public function store(SmsRequest $request)
    {
        $phone = $request->input('phone');
        $code = $request->input('code');
        $sessionKey = $request->input('sessionKey');

        $cacheCode = $this->redis->get($sessionKey);
        if (!$cacheCode || $cacheCode != $code)
        {
            return $this->response->json(responseError(422, '验证码错误'));
        }

        $sendCode = str_pad((string)mt_rand(000000, 999999), 6, '0');
        $this->service->send($phone, ['code' => $sendCode]);
        $this->redis->del($sessionKey);
        $this->redis->set($phone, $sendCode, 300);
        return $this->response->json(responseSuccess());
    }

}
