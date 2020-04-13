<?php
/**
 * Created by PhpStorm.
 * User: 简美
 * Date: 2020/3/25
 * Time: 11:02
 */


function result($code, $message, $date)
{
    $result = [
        'code' => $code,
        'message' => $message,
        'data' => $date,
    ];

    return $result;
}

function responseSuccess($code = 200, $message = '', $data = [])
{
    if (empty($message))
    {
        return result($code, \App\Constants\ResponseCode::getMessage($code), $data);
    }

    return result($code, $message, $data);
}

function responseError($code = 422, $message, $data = [])
{
    if (empty($message))
    {
        return result($code, \App\Constants\ResponseCode::getMessage($code), $data);
    }

    return result($code, $message, $data);
}