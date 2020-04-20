<?php

declare(strict_types=1);

namespace App\Controller\Product;

use App\Controller\BaseController;
use App\Model\Product\Product;
use App\Request\Product\ProductRequest;

class ProductController extends BaseController
{
    public function show(ProductRequest $request)
    {
        $search =$request->input('search');
        $order = $request->input('order');
        $field = $request->input('field');
        $builder = Product::query()->where('on_sale', true);

        if ($search)
        {
            $like = "%$search%";
            $builder->where(function ($query) use ($like)
            {
                $query->where('title', 'like', $like)
                    ->orWhere('description', 'like', $like)
                    ->orWhereHas('skus', function ($query) use ($like)
                    {
                        $query->where('title', 'like', $like)
                            ->orWhere('description', 'like', $like);
                    });
            });
        }
        else
        {
            $builder->with('skus');
        }

        if ($order && $field)
        {
            $builder->orderBy($field, $order);
        }

        $data = $this->getPaginateData($builder->paginate());
        return $this->response->json(responseSuccess(200, '', $data));
    }

    public function store(ProductRequest $request)
    {
        $data = $request->validated();
        Product::query()->create($data);
        return $this->response->json(responseSuccess(201));
    }

    public function update(ProductRequest $request)
    {
        $data = $request->validated();
        $product = Product::getFirstById($data['id']);
        $product->update($data);
        return $this->response->json(responseSuccess(200, '更新成功'));
    }

    public function delete(ProductRequest $request)
    {
        $id = $request->input('id');
        Product::getFirstById($id)->delete();
        return $this->response->json(responseSuccess(201, '删除成功'));
    }
}
