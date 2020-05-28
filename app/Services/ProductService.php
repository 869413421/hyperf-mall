<?php
/**
 * Created by PhpStorm.
 * User: 简美
 * Date: 2020/4/10
 * Time: 10:32
 */

namespace App\Services;

use App\Model\CrowdfundingProduct;
use App\Model\Product;
use App\Model\ProductSku;
use Hyperf\DbConnection\Db;

class ProductService
{
    public function createProduct($productData): Product
    {
        return Db::transaction(function () use ($productData)
        {
            $productAttributes = [
                'title' => $productData['title'],
                'description' => $productData['description'],
                'image' => $productData['image'],
                'on_sale' => $productData['on_sale'],
                'price' => $productData['price'],
            ];

            $product = new Product($productAttributes);
            $product->save();


            foreach ($productData['items'] as $sku)
            {
                /** @var $productSku ProductSku */
                $productSku = $product->skus()->make($sku);
                $productSku->product()->associate($product);
                $productSku->save();
            }

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
            return $product;
        });

    }
}