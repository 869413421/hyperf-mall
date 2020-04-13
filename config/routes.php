<?php

declare(strict_types=1);

/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://doc.hyperf.io
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf-cloud/hyperf/blob/master/LICENSE
 */

use Hyperf\HttpServer\Router\Router;
use App\Middleware\JwtAuthMiddleWare;

$authMiddleWare = [
    JwtAuthMiddleWare::class,
];
//游客访问路由
Router::addGroup('', function ()
{
    Router::get('/captcha', 'App\Controller\Captcha\CaptchaController@show');
    Router::addGroup('/sms', function ()
    {
        Router::post('', 'App\Controller\Sms\SmsController@store');
    });
    //User
    Router::addGroup('/user', function ()
    {
        Router::get('/email', 'App\Controller\Email\EmailController@verifyEmail');
        Router::post('/email', 'App\Controller\Email\EmailController@sendVerifyEmail');
        Router::post('', 'App\Controller\User\UserController@store');
        Router::post('/token', 'App\Controller\Token\TokenController@store');
        Router::patch('/password', 'App\Controller\User\UserController@resetPassword');
    });

});
//用户访问路由
Router::addGroup('', function ()
{
    //User
    Router::addGroup('/user', function ()
    {
        Router::patch('', 'App\Controller\User\UserController@update');
        Router::delete('', 'App\Controller\User\UserController@delete');
        Router::post('/avatar', 'App\Controller\File\FileController@uploadAvatar');
        Router::patch('/token', 'App\Controller\Token\TokenController@update');
        Router::delete('/token', 'App\Controller\Token\TokenController@delete');
        Router::get('/addresses', 'App\Controller\User\UserAddressesController@show');
        Router::post('/addresses', 'App\Controller\User\UserAddressesController@store');
        Router::patch('/addresses', 'App\Controller\User\UserAddressesController@update');
        Router::delete('/addresses', 'App\Controller\User\UserAddressesController@delete');
    });
}, ['middleware' => $authMiddleWare]);