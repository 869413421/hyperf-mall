<?php

declare(strict_types=1);

namespace App\Job;

use App\Model\Product;
use App\Utils\ElasticSearch;
use Hyperf\AsyncQueue\Job;

class SyncProductJob extends Job
{
    /**
     * @var Product
     */
    public $product;

    public function __construct(Product $product)
    {
        $this->product = $product;
    }

    public function handle()
    {
        $es = container()->get(ElasticSearch::class);

        $index = 'products';
        $type = '_doc';

        $params = [
            'body' => []
        ];

        $params['body'][] = [
            'index' => [
                '_index' => $index,
                '_type' => $type,
                '_id' => $this->product->id,
            ],
        ];
        $params['body'][] = $this->product->toESArray();
        try
        {
            $es->bulk($params);
        }
        catch (\Exception $e)
        {
            var_dump($e->getMessage());
        }

    }
}
