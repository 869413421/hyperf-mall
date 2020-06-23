<?php

declare(strict_types=1);

namespace App\Controller;

use App\Exception\ServiceException;
use App\Model\CrowdfundingProduct;
use App\Model\Product;
use App\Request\CrowdfundingRequest;
use App\Services\ProductService;
use Hyperf\Di\Annotation\Inject;

class CrowdfundingController extends BaseController
{
    /**
     * @Inject()
     * @var ProductService
     */
    private $productService;

    public function index()
    {
        $query = Product::query()->with(['crowdfunding', 'skus'])->where('type', Product::TYPE_CROWDFUNDING);
        $data = $this->getPaginateData($query->paginate($this->getPageSize()));
        return $this->response->json(responseSuccess(200, '', $data));
    }

    public function store(CrowdfundingRequest $request)
    {
        $productData = $request->validated();
        $product = $this->productService->createProduct($productData);
        return $this->response->json(responseSuccess(201, '', $product));
    }

    public function update(CrowdfundingRequest $request)
    {
        $product = Product::getFirstById($request->route('id'));
        if (!$product || $product->type != Product::TYPE_CROWDFUNDING)
        {
            throw new ServiceException(403, '商品不存在');
        }
        $data = $request->validated();
        $product = $this->productService->updateProduct($product, $data);
        return $this->response->json(responseSuccess(200, '更新成功', $product));
    }

    public function delete()
    {
        $crowdfunding = CrowdfundingProduct::getFirstById($this->request->route('id'));
        if (!$crowdfunding)
        {
            throw new ServiceException(403, '商品不存在');
        }
        $crowdfunding->delete();
        return $this->response->json(responseSuccess(200, '删除成功'));
    }
}
