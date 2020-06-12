<?php

declare(strict_types=1);

namespace App\Command;

use App\Model\Product;
use App\Utils\ElasticSearch;
use Hyperf\Command\Command as HyperfCommand;
use Hyperf\Command\Annotation\Command;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Input\InputArgument;

/**
 * 同步商品信息到ElasticSearch
 * @Command
 */
class SyncProducts extends HyperfCommand
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;

        parent::__construct('es:sync-products');
    }

    public function configure()
    {
        parent::configure();
        $this->setDescription('同步商品信息到elasticSearch');
    }

    protected function getArguments()
    {
        return [
            ['index', InputArgument::OPTIONAL, '索引名称']
        ];
    }

    public function handle()
    {
        // 从 $input 获取 name 参数
        $index = $this->input->getArgument('index') ?? 'products';
        $this->info('准备同步索引:' . $index);
        $es = $this->container->get(ElasticSearch::class);
        Product::query()
            // 预加载 SKU 和 商品属性数据，避免 N + 1 问题
            ->with(['skus', 'properties'])
            // 使用 chunkById 避免一次性加载过多数据
            ->chunkById(100, function ($products) use ($es, $index)
            {
                $this->info(sprintf('正在同步 ID 范围为 %s 至 %s 的商品', $products->first()->id, $products->last()->id));

                $type = '_doc';

                $params = [
                    'body' => []
                ];

                // 遍历商品
                foreach ($products as $product)
                {
                    // 将商品模型转为 Elasticsearch 所用的数组
                    $params['body'][] = [
                        'index' => [
                            '_index' => $index,
                            '_type' => $type,
                            '_id' => $product->id,
                        ],
                    ];

                    $params['body'][] = $product->toEsArray();
                }

                try
                {
                    $es->bulk($params);
                }
                catch (\Exception $e)
                {
                    $this->error($e->getMessage());
                }
            });
        $this->info('同步完成');

    }
}
