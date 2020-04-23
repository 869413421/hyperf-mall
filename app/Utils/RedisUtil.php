<?php
/**
 * Created by PhpStorm.
 * User: 简美
 * Date: 2020/4/1
 * Time: 14:57
 */

namespace App\Utils;

use Hyperf\Redis\Redis;
use Hyperf\Redis\RedisFactory;
use Hyperf\Utils\ApplicationContext;

class RedisUtil
{
    private $redis;
    private $container;

    public function __construct()
    {
        $this->container = ApplicationContext::getContainer();
        $this->redis = $this->container->get(Redis::class);
    }

    public function set($key, $value, $ttl = 300)
    {
        return $this->redis->setex($key, $ttl, $value);
    }

    public function get($key)
    {
        return $this->redis->get($key);
    }

    public function del($key)
    {
        if (!$this->redis->exists($key))
        {
            return false;
        }

        return $this->redis->del($key);
    }
}