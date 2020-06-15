<?php
/**
 * Created by PhpStorm.
 * User: 简美
 * Date: 2020/5/8
 * Time: 17:38
 */

namespace App\Event;

use App\Model\SeckillProduct;

class SavedSeckillEvent
{
    public $seckillProduct;

    public function __construct(SeckillProduct $seckillProduct)
    {
        $this->seckillProduct = $seckillProduct;
    }
}