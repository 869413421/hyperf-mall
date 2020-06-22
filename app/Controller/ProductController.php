<?php

declare(strict_types=1);

namespace App\Controller;

use App\Exception\ServiceException;
use App\Model\Category;
use App\Model\Product;
use App\Model\ProductProperty;
use App\Model\User;
use App\Request\FavorRequest;
use App\Request\ProductRequest;
use App\SearchBuilders\ProductSearchBuilder;
use App\Services\ProductService;
use App\Utils\ElasticSearch;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Paginator\LengthAwarePaginator;

class ProductController extends BaseController
{
    /**
     * @Inject()
     * @var ProductService
     */
    private $productService;

    /**
     * @Inject()
     * @var ElasticSearch
     */
    private $es;

    public function index(ProductRequest $request)
    {
        $search = $request->input('search');
        $order = $request->input('order');
        $field = $request->input('field');
        $category_id = $request->input('category_id');
        $type = $request->input('type');
        $builder = Product::query();

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
        if ($category_id)
        {
            $builder->where('category_id', $category_id);
        }
        if ($type)
        {
            $builder->where('type', $type);
        }
        $builder->with('category')->with('skus')->with('crowdfunding')->with('seckill');


        if ($order && $field)
        {
            $builder->orderBy($field, $order);
        }

        $data = $this->getPaginateData($builder->paginate());
        return $this->response->json(responseSuccess(200, '', $data));
    }

    public function show()
    {
        $product = Product::with('skus')->where('id', $this->request->route('id'))->first();
        if (!$product)
        {
            throw new ServiceException(422, '商品不存在');
        }
        if (!$product->on_sale)
        {
            throw new ServiceException(422, '商品没上架');
        }

        //获取推荐商品
        $similarProductIds = $this->productService->getSimilarProductIds($product, 4);
        // 根据 Elasticsearch 搜索出来的商品 ID 从数据库中读取商品数据
        $similarProducts = Product::query()->byIds($similarProductIds)->get();

        $data = [
            'product' => $product,
            'similarProducts' => $similarProducts
        ];

        return $this->response->json(responseSuccess(200, '', $data));
    }

    public function productList()
    {
        $page = $this->request->input('page', 1);
        $perPage = $this->getPageSize();

        $builder = (new ProductSearchBuilder())->onSale()->paginate($page, $perPage);

        // 是否有提交 order 参数，如果有就赋值给 $order 变量
        // order 参数用来控制商品的排序规则
        if ($order = $this->request->input('order', ''))
        {
            // 是否是以 _asc 或者 _desc 结尾
            if (preg_match('/^(.+)_(asc|desc)$/', $order, $m))
            {
                // 如果字符串的开头是这 3 个字符串之一，说明是一个合法的排序值
                if (in_array($m[1], ['price', 'sold_count', 'rating']))
                {
                    // 根据传入的排序值来构造排序参数
                    $builder->orderBy($m[1], $m[2]);
                }
            }
        }

        if ($this->request->input('category_id') && $category = Category::find($this->request->input('category_id')))
        {
            $builder->category($category);
        }

        //关键词搜索
        if ($search = $this->request->input('search', ''))
        {
            // 将搜索词根据空格拆分成数组，并过滤掉空项
            $keywords = array_filter(explode(' ', $search));
            $builder->keywords($keywords);
        }

        if ($search || isset($category))
        {
            //分面聚合查询
            $builder->aggregateProperties();
        }

        $propertyFilters = [];
        // 从用户请求参数获取 filters
        if ($filterString = $this->request->input('filters'))
        {
            // 将获取到的字符串用符号 | 拆分成数组
            $filterArray = explode('|', $filterString);
            foreach ($filterArray as $filter)
            {
                // 将字符串用符号 : 拆分成两部分并且分别赋值给 $name 和 $value 两个变量
                list($name, $value) = explode(':', $filter);
                $propertyFilters[$name] = $value;
                // 添加到 filter 类型中
                $builder->propertyFilter($name . ':' . $value);
            }
        }

        $result = $this->es->es_client->search($builder->getParams());

        $properties = [];
        // 如果返回结果里有 aggregations 字段，说明做了分面搜索
        if (isset($result['aggregations']))
        {
            // 使用 collect 函数将返回值转为集合
            $properties = collect($result['aggregations']['properties']['properties_name']['buckets'])
                ->map(function ($bucket)
                {
                    // 通过 map 方法取出我们需要的字段
                    return [
                        'key' => $bucket['key'],
                        'values' => collect($bucket['properties_value']['buckets'])->pluck('key')->all(),
                    ];
                })->filter(function ($property) use ($propertyFilters)
                {
                    // 过滤掉只剩下一个值 或者 已经在筛选条件里的属性
                    return count($property['values']) > 1 && !isset($propertyFilters[$property['key']]);
                });
        }

        // 通过 collect 函数将返回结果转为集合，并通过集合的 pluck 方法取到返回的商品 ID 数组
        $productIds = collect($result['hits']['hits'])->pluck('_id')->all();
        // 通过 whereIn 方法从数据库中读取商品数据
        $products = Product::query()->byIds($productIds)->get();

        $data = $this->getPaginateData(new LengthAwarePaginator($products, (int)$result['hits']['total']['value'], $perPage, $page));
        $data['properties'] = $properties;
        return $this->response->json(responseSuccess(200, '', $data));
    }

    public function store(ProductRequest $request)
    {
        $product = $this->productService->createProduct($request->validated());
        return $this->response->json(responseSuccess(201, '', $product));
    }

    public function update(ProductRequest $request)
    {
        $product = Product::getFirstById($request->route('id'));
        if (!$product)
        {
            throw new ServiceException(403, '商品不存在');
        }
        $product = $this->productService->updateProduct($product, $request->validated());
        return $this->response->json(responseSuccess(200, '更新成功', $product));
    }

    public function delete(ProductRequest $request)
    {
        $product = Product::getFirstById($request->route('id'));
        if (!$product)
        {
            throw new ServiceException(403, '商品不存在');
        }
        $product->delete();
        return $this->response->json(responseSuccess(201, '删除成功'));
    }

    public function favor(FavorRequest $request)
    {
        $productId = $request->route('id');
        $product = Product::getFirstById($productId);
        if (!$product)
        {
            throw new ServiceException(403, '商品不存在');
        }

        /** @var $user User */
        $user = $request->getAttribute('user');
        if ($user->favoriteProducts()->find($productId))
        {
            throw new ServiceException(403, '已经收藏过本商品');
        }

        $user->favoriteProducts()->attach($productId);
        return $this->response->json(responseSuccess(201, '收藏成功'));
    }

    public function detach(FavorRequest $request)
    {
        $productId = $request->route('id');
        $product = Product::getFirstById($productId);
        if (!$product)
        {
            throw new ServiceException(403, '商品不存在');
        }

        /** @var $user User */
        $user = $request->getAttribute('user');
        if (!$user->favoriteProducts()->find($productId))
        {
            throw new ServiceException(403, '没有收藏过本商品');
        }

        $user->favoriteProducts()->detach($productId);
        return $this->response->json(responseSuccess(201, '取消成功'));
    }

    public function favorites()
    {
        /** @var $user User */
        $user = $this->request->getAttribute('user');
        $data = $this->getPaginateData($user->favoriteProducts()->paginate());
        return $this->response->json(responseSuccess(200, '', $data));
    }
}
