<?php

declare(strict_types=1);

namespace App\Controller;

use App\Exception\ServiceException;
use App\Model\Product;
use App\Model\ProductSku;
use App\Request\ProductRequest;
use App\Request\ProductSkuRequest;

class ProductSkuController extends BaseController
{
    public function store(ProductSkuRequest $request)
    {
        $product = Product::getFirstById($request->route('id'));
        if (!$product)
        {
            throw new ServiceException(403, '商品不存在');
        }

        /** @var $productSku ProductSku */
        $productSku = $product->skus()->make($request->validated());
        $productSku->product()->associate($product);
        $productSku->save();
        return $this->response->json(responseSuccess(201));
    }

    public function update(ProductRequest $request)
    {
        $data = $request->validated();
        $productSku = ProductSku::getFirstById($request->route('sku_id'));
        if (!$productSku)
        {
            throw new ServiceException(403, '商品SKU不存在');
        }
        $productSku->update($data);
        return $this->response->json(responseSuccess(200, '更新成功'));
    }

    public function delete(ProductRequest $request)
    {
        $productSku = ProductSku::getFirstById($request->route('sku_id'));
        if (!$productSku)
        {
            throw new ServiceException(403, '商品SKU不存在');
        }
        $productSku->delete();
        return $this->response->json(responseSuccess(201, '删除成功'));
    }
}
