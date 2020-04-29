<?php

declare(strict_types=1);

namespace App\Controller;

use App\Facade\Redis;
use App\Handler\Sms\SmsInterface;
use App\Request\SmsRequest;
use Hyperf\Di\Annotation\Inject;

class SmsController extends BaseController
{
    /**
     * @Inject
     * @var SmsInterface
     */
    private $service;

    public function store(SmsRequest $request)
    {
        $phone = $request->input('phone');
        $code = $request->input('code');
        $sessionKey = $request->input('sessionKey');

        $cacheCode = Redis::get($sessionKey);
        if (!$cacheCode || $cacheCode != $code)
        {
            return $this->response->json(responseError(422, '验证码错误'));
        }

        $sendCode = str_pad((string)mt_rand(000000, 999999), 6, '0');
        $this->service->send($phone, ['code' => $sendCode]);
        Redis::del($sessionKey);
        Redis::set($phone, $sendCode, 300);
        return $this->response->json(responseSuccess());
    }

}
