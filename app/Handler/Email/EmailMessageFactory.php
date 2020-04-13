<?php
/**
 * Created by PhpStorm.
 * User: 简美
 * Date: 2020/3/31
 * Time: 14:32
 */

namespace App\Handler\Email;


use Hyperf\Contract\ConfigInterface;
use Psr\Container\ContainerInterface;

class EmailMessageFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $config = $container->get(ConfigInterface::class);
        $option = $config->get('email.default', []);
        return make(EmailMessageHandler::class, compact('option'));
    }
}