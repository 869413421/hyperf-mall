<?php

use Hyperf\Crontab\Crontab;

return [
    // 是否开启定时任务
    'enable' => true,
    'crontab' => [
        //每天0点定时检查众筹是否结束
        (new Crontab())->setType('command')->setName('结束众筹定时任务')->setRule('* 0 * * * *')->setCallback([
            'command' => 'finish:crowdfunding',
        ]),
        //每天0点定时计算逾期订单逾期费用
        (new Crontab())->setType('command')->setName('计算逾期订单费用')->setRule('* 0 * * * *')->setCallback([
            'command' => 'cron:calculate-installment-fine',
        ]),
    ]
];