<?php

use Hyperf\Crontab\Crontab;

return [
    // 是否开启定时任务
    'enable' => true,
    'crontab' => [
        //每天0点定时检查众筹是否结束
        (new Crontab())->setType('command')->setName('结束众筹定时任务')->setRule('* 21 * * * *')->setCallback([
            'command' => 'finish:crowdfunding',
        ]),
    ]
];