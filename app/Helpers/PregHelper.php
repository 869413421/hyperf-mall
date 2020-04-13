<?php
/**
 * Created by PhpStorm.
 * User: 简美
 * Date: 2020/3/30
 * Time: 16:46
 */
function isPhone($str)
{
    if (preg_match('/^1(?:3\d|4[4-9]|5[0-35-9]|6[67]|7[013-8]|8\d|9\d)\d{8}$/', $str))
    {
        return true;
    }

    return false;
}

function isEmail($str)
{
    $pattern = "/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,})$/";
    if (preg_match($pattern, $str))
    {
        return true;
    }

    return false;
}