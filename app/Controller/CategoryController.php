<?php

declare(strict_types=1);

namespace App\Controller;

use App\Exception\ServiceException;
use App\Model\Category;
use App\Request\CategoryRequest;
use App\Services\CategoryService;
use Hyperf\Di\Annotation\Inject;

class CategoryController extends BaseController
{
    /**
     * @Inject()
     * @var CategoryService
     */
    private $categoryService;

    public function menu()
    {
        $data = $this->categoryService->getCategoryTree();
        return $this->response->json(responseSuccess(200, '', $data));
    }

    public function index()
    {
        $query = Category::query();
        $data = $this->getPaginateData($query->paginate($this->getPageSize()));
        return $this->response->json(responseSuccess(200, '', $data));
    }

    public function store(CategoryRequest $request)
    {
        $category = new Category($request->validated());
        $category->save();
        return $this->response->json(responseSuccess(201, '', $category));
    }

    public function update(CategoryRequest $request)
    {
        $category = Category::getFirstById($request->route('id'));
        if (!$category)
        {
            throw new ServiceException(403, '分类不存在');
        }
        $category->update($request->validated());
        return $this->response->json(responseSuccess(200, '更新成功', $category));
    }

    public function delete()
    {
        $category = Category::getFirstById($this->request->route('id'));
        if (!$category)
        {
            throw new ServiceException(403, '分类不存在');
        }
        $category->delete();
        return $this->response->json(responseSuccess(200, '删除成功'));
    }
}
