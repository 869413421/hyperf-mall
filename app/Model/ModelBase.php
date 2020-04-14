<?php


namespace App\Model;


class ModelBase extends Model implements ModelInterface
{
    /**
     * 根据主键获取一个实体
     * @param  $id *主键
     * @return static
     */
    public static function getFirstById($id)
    {
        return self::query()->where('id', $id)->first();
    }

    /**
     * 根据条件获取一个实体
     * @param array $where *条件数组
     * @param array $select *显示字段
     * @return static
     */
    public static function getFirstByWhere(array $where, $select = ['*'])
    {
        return self::query()->where($where)->first($select);
    }

    /**
     * 获取实体的所有数据
     * @param array $where *条件数组
     * @param array $select *显示字段
     * @param bool $needToArray *是否序列化成数组
     * @return static
     */
    public static function getAllData(array $where, $select = ['*'], bool $needToArray = false)
    {
        $query = self::query()->where($where)->select($select);

        if ($needToArray)
        {
            return $query->get()->toArray();
        }

        return $query->get();
    }

    /**
     * 获取模型分页数据
     * @param array $where *条件数组
     * @param int $pageSize *页数
     * @param array $select *显示字段
     * @param string $order *排序字段
     * @param string $sort *排序方式
     * @return \Hyperf\Contract\LengthAwarePaginatorInterface
     */
    public static function getList(array $where=[], $pageSize = 20, $select = ['*'], $order = 'created_at', $sort = 'DESC')
    {
        return self::query()->where($where)->select($select)->orderBy($order, $sort)->paginate($pageSize);
    }
}