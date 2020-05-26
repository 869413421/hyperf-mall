<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\Category;
use App\Request\CategoryRequest;

class CategoryController extends BaseController
{
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

    public function update()
    {

    }

    public function delete()
    {

    }
}
