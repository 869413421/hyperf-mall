<?php


namespace App\Utils;


use Hyperf\Elasticsearch\ClientBuilderFactory;

class ElasticSearchUtil
{
    /***
     * 获取elastic search客户端
     * @return \Elasticsearch\ClientBuilder
     */
    public static function client()
    {
        $builder = container()->get(ClientBuilderFactory::class)->create();
        $client = $builder->setHosts(config('databases.elasticsearch.hosts'));
        return $client;
    }
}