<?php

declare(strict_types=1);

namespace App\Controller;

use App\Exception\ServiceException;
use App\Model\Product;
use App\Model\SeckillProduct;
use App\Request\SeckillProductsRequest;
use App\Services\ProductService;
use Hyperf\Di\Annotation\Inject;

class SeckillProductsController extends BaseController
{
    /**
     * @Inject()
     * @var ProductService
     */
    private $productService;

    public function index()
    {
        $query = Product::query()->with(['seckill', 'skus'])->where('type', Product::TYPE_SECKILL);
        $data = $this->getPaginateData($query->paginate($this->getPageSize()));
        return $this->response->json(responseSuccess(200, '', $data));
    }

    public function store(SeckillProductsRequest $request)
    {
        $productData = $request->validated();
        $product = $this->productService->createProduct($productData);
        return $this->response->json(responseSuccess(201, '', $product));
    }

    public function update(SeckillProductsRequest $request)
    {
        $product = Product::getFirstById($request->route('id'));
        if (!$product || $product->type != Product::TYPE_SECKILL)
        {
            throw new ServiceException(403, '商品不存在');
        }
        $data = $request->validated();
        $product = $this->productService->updateProduct($product, $data);
        return $this->response->json(responseSuccess(200, '更新成功', $product));
    }

    public function delete()
    {
        $seckillProduct = SeckillProduct::getFirstById($this->request->route('id'));
        if (!$seckillProduct)
        {
            throw new ServiceException(403, '商品不存在');
        }
        $seckillProduct->delete();
        return $this->response->json(responseSuccess(200, '删除成功'));
    }
}
