<?php
/**
 * Created by PhpStorm.
 * User: 简美
 * Date: 2020/4/1
 * Time: 14:57
 */

namespace App\Facade;

use Hyperf\Redis\Redis as RedisBase;

class Redis
{
    public static function getRedis()
    {
        return container()->get(RedisBase::class);
    }

    public static function __callStatic($method, $arguments)
    {
        $redis = self::getRedis();
        
        return $redis->$method(...$arguments);
    }
}