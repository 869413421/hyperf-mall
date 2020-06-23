<?php
/**
 * Created by PhpStorm.
 * User: 简美
 * Date: 2020/4/10
 * Time: 10:32
 */

namespace App\Services;

use App\Event\SavedSeckillEvent;
use App\Model\CrowdfundingProduct;
use App\Model\Product;
use App\Model\ProductSku;
use App\Model\SeckillProduct;
use App\SearchBuilders\ProductSearchBuilder;
use App\Utils\ElasticSearch;
use Hyperf\DbConnection\Db;
use Hyperf\Di\Annotation\Inject;
use Psr\EventDispatcher\EventDispatcherInterface;

class ProductService
{
    /**
     * @Inject()
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    public function createProduct($productData): Product
    {
        return Db::transaction(function () use ($productData)
        {
            $category_id = $productData['category_id'] ?? null;
            $productAttributes = [
                'title' => $productData['title'],
                'long_title' => $productData['long_title'],
                'description' => $productData['description'],
                'image' => $productData['image'],
                'on_sale' => $productData['on_sale'],
                'price' => $productData['price'],
                'category_id' => $category_id
            ];


            $product = new Product($productAttributes);
            $product->save();

            $properties = $productData['properties'] ?? [];
            foreach ($properties as $property)
            {
                $productProperty = $product->properties()->make($property);
                $productProperty->save();
            }

            foreach ($productData['items'] as $sku)
            {
                /** @var $productSku ProductSku */
                $productSku = $product->skus()->make($sku);
                $productSku->product()->associate($product);
                $productSku->save();
            }

            //众筹商品
            if (key_exists('target_amount', $productData))
            {
                $crowdfunding = new CrowdfundingProduct();
                $crowdfunding->target_amount = $productData['target_amount'];
                $crowdfunding->end_time = $productData['end_time'];
                $crowdfunding->product()->associate($product);
                $crowdfunding->save();
                $product->type = Product::TYPE_CROWDFUNDING;
                $product->save();
            }

            //秒杀商品
            if (key_exists('start_at', $productData) && key_exists('end_at', $productData))
            {
                $seckillProduct = new SeckillProduct();
                $seckillProduct->start_at = $productData['start_at'];
                $seckillProduct->end_at = $productData['end_at'];
                $seckillProduct->product()->associate($product);
                $seckillProduct->save();
                $product->type = Product::TYPE_SECKILL;
                $product->save();
                //触发秒杀商品保存事件
                $this->eventDispatcher->dispatch(new SavedSeckillEvent($seckillProduct));
            }
            $product = Product::with('skus', 'category', 'crowdfunding', 'seckill')->where('id', $product->getKey())->first();
            return $product;
        });

    }

    public function updateProduct(Product $product, array $updateDate)
    {
        $product->fill($updateDate);
        $product->save();

        $skus = $updateDate['items'] ?? [];
        var_dump($skus);
        if ($skus)
        {
            $skuIds = collect($skus)->pluck('id')->toArray();
            var_dump(1111);
            var_dump($skuIds);
            ProductSku::query()->where('product_id', $product->getKey())->whereNotIn('id', $skuIds)->delete();
        }
        foreach ($updateDate['items'] as $sku)
        {
            $skuId = $sku['id'] ?? null;
            if ($skuId)
            {
                ProductSku::query()->where('id', $skuId)->update($sku);
            }
            else
            {
                $product->skus()->make($sku)->save();
            }
        }
        //众筹商品
        if (key_exists('target_amount', $updateDate))
        {
            $product->crowdfunding->fill($updateDate);
            $product->crowdfunding->save();
        }
        //秒杀商品
        if (key_exists('start_at', $updateDate) && key_exists('end_at', $updateDate))
        {
            $seckillProduct = $product->seckill;
            $seckillProduct->fill($updateDate);
            $seckillProduct->save();
            //触发秒杀商品保存事件
            $this->eventDispatcher->dispatch(new SavedSeckillEvent($seckillProduct));
        }
        $product = Product::with('skus', 'category', 'crowdfunding', 'seckill')->where('id', $product->getKey())->first();
        return $product;
    }

    /**
     * 获取相似商品
     * @param Product $product
     * @param int $pageSize
     * @return array
     */
    public function getSimilarProductIds(Product $product, int $pageSize)
    {
        $es = container()->get(ElasticSearch::class);
        // 如果商品没有商品属性，则直接返回空
        if (count($product->properties) === 0)
        {
            return [];
        }
        $builder = (new ProductSearchBuilder())->onSale()->paginate(1, $pageSize);
        foreach ($product->properties as $property)
        {
            $builder->propertyFilter($property->name . ':' . $property->value, 'should');
        }
        $builder->minShouldMatch(ceil(count($product->properties) / 2));
        $params = $builder->getParams();
        $params['body']['query']['bool']['must_not'] = [['term' => ['_id' => $product->id]]];
        $result = $es->es_client->search($params);

        return collect($result['hits']['hits'])->pluck('_id')->all();
    }
}