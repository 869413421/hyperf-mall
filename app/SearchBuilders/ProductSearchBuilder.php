<?php
/**
 * Created by PhpStorm.
 * User: 简美
 * Date: 2020/6/11
 * Time: 17:03
 */

namespace App\SearchBuilders;


use App\Model\Category;

class ProductSearchBuilder
{
    protected $params = [
        'index' => 'products',
        'type' => '_doc',
        'body' => [
            'query' => [
                'bool' => [
                    //filter和must都等同与SQL的and，但是filter不参与打分，must参与打分
                    'filter' => [],
                    'must' => [],
                ]
            ]
        ],
    ];

    /**
     * 分页
     * @param int $page *页码
     * @param int $pageSize *分页条数
     * @return ProductSearchBuilder
     */
    public function paginate(int $page, int $pageSize): ProductSearchBuilder
    {
        $this->params['body']['from'] = ($page - 1) * $pageSize;
        $this->params['body']['size'] = $pageSize;
        return $this;
    }

    /**
     * 获取上架商品
     * @return ProductSearchBuilder
     */
    public function onSale(): ProductSearchBuilder
    {
        $this->params['body']['query']['bool']['filter'][] = ['term' => ['on_sale' => true]];
        return $this;
    }

    /**
     * 根据分类进行查找
     * @param Category $category
     * @return ProductSearchBuilder
     */
    public function category(Category $category): ProductSearchBuilder
    {
        if ($category->is_directory)
        {
            $this->params['body']['query']['bool']['filter'][] = [
                'prefix' => ['category_path' => $category->path . $category->id . '-']
            ];
        }
        else
        {
            $this->params['body']['query']['bool']['filter'][] = ['term' => ['category_id' => $category->id]];
        }
        return $this;
    }

    /**
     * 根据关键词查询
     * @param array $keywords *关键词数组
     * @return ProductSearchBuilder
     */
    public function keywords(array $keywords): ProductSearchBuilder
    {
        // 如果参数不是数组则转为数组
        $keywords = is_array($keywords) ? $keywords : [$keywords];
        foreach ($keywords as $keyword)
        {
            $this->params['body']['query']['bool']['must'][] = [
                'multi_match' => [
                    'query' => $keyword,
                    'fields' => [
                        'title^3',
                        'long_title^2',
                        'category^2',
                        'description',
                        'skus.title',
                        'skus.description',
                        'properties.value',
                    ],
                ],
            ];
        }
        return $this;
    }

    /**
     * 聚合商品属性
     * @return ProductSearchBuilder
     */
    public function aggregateProperties(): ProductSearchBuilder
    {
        $this->params['body']['aggs'] = [
            'properties' => [
                'nested' => [
                    'path' => 'properties'
                ],
                'aggs' => [
                    'properties_name' => [
                        'terms' => [
                            'field' => 'properties.name',
                        ],
                        'aggs' => [
                            'properties_value' => [
                                'terms' => [
                                    'field' => 'properties.value'
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];
        return $this;
    }

    /**
     * 添加一个按商品属性筛选的条件
     * @param string $searchValue
     * @return ProductSearchBuilder
     */
    public function propertyFilter(string $searchValue, string $type = 'filter'): ProductSearchBuilder
    {
        $this->params['body']['query']['bool'][$type][] = [
            'nested' => [
                'path' => 'properties',
                'query' => [
                    ['term' => ['properties.search_value' => $searchValue]],
                ]
            ]
        ];
        return $this;
    }

    /**
     * 排序
     * @param $field
     * @param $direction
     * @return ProductSearchBuilder
     */
    public function orderBy($field, $direction): ProductSearchBuilder
    {
        if (!isset($this->params['body']['sort']))
        {
            $this->params['body']['sort'] = [];
        }
        $this->params['body']['sort'][] = [$field => $direction];

        return $this;
    }

    /**
     * 设置 minimum_should_match 参数
     * @param int $count
     * @return $this
     */
    public function minShouldMatch(int $count)
    {
        $this->params['body']['query']['bool']['minimum_should_match'] = $count;

        return $this;
    }

    /**
     * 返回构造好的参数
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }
}