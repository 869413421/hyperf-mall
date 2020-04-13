<?php
/**
 * Created by PhpStorm.
 * User: 简美
 * Date: 2020/4/2
 * Time: 12:49
 */

namespace App\Handler\Sms;


interface SmsInterface
{
    public function send($phone, $params = []);
}