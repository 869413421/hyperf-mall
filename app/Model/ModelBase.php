<?php


namespace App\Model;


class ModelBase extends Model implements ModelInterface
{
    /**
     * 根据主键获取一个实体
     * @param  $id
     * @return static
     */
    public static function getFirstById($id)
    {
        return self::query()->where('id', $id)->first();
    }

    /**
     * 根据条件获取一个实体
     * @param array $where 条件数组
     * @param array $select 显示字段
     * @return static
     */
    public static function getFirstByWhere(array $where, $select = ['*'])
    {
        return self::query()->where($where)->first($select);
    }
}