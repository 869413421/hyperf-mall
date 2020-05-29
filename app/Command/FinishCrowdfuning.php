<?php

declare(strict_types=1);

namespace App\Command;

use App\Model\CrowdfundingProduct;
use Carbon\Carbon;
use Hyperf\Command\Command as HyperfCommand;
use Hyperf\Command\Annotation\Command;
use Psr\Container\ContainerInterface;

/**
 * @Command
 */
class FinishCrowdfuning extends HyperfCommand
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;

        parent::__construct('finish:crowdfunding');
    }

    public function configure()
    {
        parent::configure();
        $this->setDescription('结束众筹');
    }

    public function handle()
    {
        $this->line('finish crowdfunding start', 'info');
        CrowdfundingProduct::query()
            ->with('product')
            ->where('status', CrowdfundingProduct::STATUS_FUNDING)
            ->where('end_time', '<', Carbon::now())
            ->each(function (CrowdfundingProduct $crowdfunding)
            {

            });
    }
}
