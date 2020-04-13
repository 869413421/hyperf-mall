<?php


namespace App\Model;


interface ModelInterface
{
    public static function getFirstById($id);

    public static function getFirstByWhere(array $where, $select = ['*']);
}