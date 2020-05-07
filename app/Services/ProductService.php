<?php
/**
 * Created by PhpStorm.
 * User: 简美
 * Date: 2020/4/10
 * Time: 10:32
 */

namespace App\Services;

use App\Model\Product;
use App\Model\ProductSku;

class ProductService
{
    public function createProduct($productData): Product
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

        return $product;
    }
}