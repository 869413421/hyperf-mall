<?php

declare(strict_types=1);

namespace App\Command;

use App\Utils\ElasticSearch;
use App\Utils\ProjectIndex;
use Hyperf\Command\Command as HyperfCommand;
use Hyperf\Command\Annotation\Command;
use Psr\Container\ContainerInterface;

/**
 * Es索引结构迁移命令
 * @Command
 */
class ElasticsearchMigrate extends HyperfCommand
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /***
     * @var \Elasticsearch\Client
     */
    protected $esClient;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;

        parent::__construct('es:migrate');
    }

    public function configure()
    {
        parent::configure();
        $this->setDescription('Elasticsearch 索引结构迁移');
    }

    public function handle()
    {

        $this->esClient = $this->container->get(ElasticSearch::class)->es_client;
        // 索引类数组，先留空
        $indices = [ProjectIndex::class];;
        // 遍历索引类数组
        foreach ($indices as $indexClass)
        {
            // 调用类数组的 getAliasName() 方法来获取索引别名
            $aliasName = $indexClass::getAliasName();
            $this->info('正在处理索引 ' . $aliasName);
            // 通过 exists 方法判断这个别名是否存在
            if (!$this->esClient->indices()->exists(['index' => $aliasName]))
            {
                $this->info('索引不存在，准备创建');
                $this->createIndex($aliasName, $indexClass);
                $this->info('创建成功，准备初始化数据');
                $this->rebuild($aliasName);
                $this->info('操作成功');
                continue;
            }
            // 如果索引已经存在，那么尝试更新索引，如果更新失败会抛出异常
            try
            {
                $this->info('索引存在，准备更新');
                $this->updateIndex($aliasName, $indexClass);
            }
            catch (\Exception $e)
            {
                $this->warn('更新失败，准备重建');
                $this->reCreateIndex($aliasName, $indexClass);
            }
            $this->info($aliasName . ' 操作成功');
        }
    }

    protected function createIndex($aliasName, $indexClass)
    {
        // 调用 create() 方法创建索引
        $this->esClient->indices()->create([
            // 第一个版本的索引名后缀为 _0
            'index' => $aliasName . '_0',
            'body' => [
                // 调用索引类的 getSettings() 方法获取索引设置
                'settings' => $indexClass::getSettings(),
                'mappings' => [
                    'properties' => $indexClass::getProperties(),
                ],
                'aliases' => [
                    // 同时创建别名
                    $aliasName => new \stdClass(),
                ],
            ],
        ]);
    }

    protected function updateIndex($aliasName, $indexClass)
    {
        // 暂时关闭索引
        $this->esClient->indices()->close(['index' => $aliasName]);
        // 更新索引设置
        $this->esClient->indices()->putSettings([
            'index' => $aliasName,
            'body' => $indexClass::getSettings(),
        ]);
        // 更新索引字段
        $this->esClient->indices()->putMapping([
            'index' => $aliasName,
            'body' => [
                'properties' => $indexClass::getProperties(),
            ],
        ]);
        // 重新打开索引
        $this->esClient->indices()->open(['index' => $aliasName]);
    }

    protected function reCreateIndex($aliasName, $indexClass)
    {
        // 获取索引信息，返回结构的 key 为索引名称，value 为别名
        $indexInfo = $this->esClient->indices()->getAliases(['index' => $aliasName]);
        // 取出第一个 key 即为索引名称
        $indexName = array_keys($indexInfo)[0];
        // 用正则判断索引名称是否以 _数字 结尾
        if (!preg_match('~_(\d+)$~', $indexName, $m))
        {
            $msg = '索引名称不正确:' . $indexName;
            $this->error($msg);
            throw new \Exception($msg);
        }
        // 新的索引名称
        $newIndexName = $aliasName . '_' . ($m[1] + 1);
        $this->info('正在创建索引' . $newIndexName);
        $this->esClient->indices()->create([
            'index' => $newIndexName,
            'body' => [
                'settings' => $indexClass::getSettings(),
                'mappings' => [
                    'properties' => $indexClass::getProperties(),
                ],
            ],
        ]);
        $this->info('创建成功，准备重建数据');
        $this->rebuild($newIndexName);
        $this->info('重建成功，准备修改别名');
        $this->esClient->indices()->putAlias(['index' => $newIndexName, 'name' => $aliasName]);
        $this->info('修改成功，准备删除旧索引');
        $this->esClient->indices()->delete(['index' => $indexName]);
        $this->info('删除成功');
    }

    public function rebuild($indexName)
    {
        $this->call('es:sync-products', [$indexName]);
    }
}
