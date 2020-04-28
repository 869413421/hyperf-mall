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
use App\Middleware\PermissionMiddleware;

$authMiddleWare = [
    JwtAuthMiddleWare::class,
];

Router::addServer('ws', function ()
{
    Router::get('/', Firstphp\Wsdebug\Wsdebug::class);
});

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

    Router::get('/product', 'App\Controller\Product\ProductController@index');
    Router::get('/product/{id}', 'App\Controller\Product\ProductController@show');
    Router::get('/ali/pay/web', 'App\Controller\AliPayController@aliPayReturn');
    Router::post('/ali/pay/web/service', 'App\Controller\AliPayController@aliPayNotify');

});
//用户访问路由
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

    Router::get('/product/collect', 'App\Controller\Product\ProductController@favorites');
    Router::post('/product/collect', 'App\Controller\Product\ProductController@favor');
    Router::delete('/product/collect', 'App\Controller\Product\ProductController@detach');

    Router::get('/cart', 'App\Controller\CartController@index');
    Router::post('/cart', 'App\Controller\CartController@store');
    Router::delete('/cart', 'App\Controller\CartController@delete');

    Router::get('/order', 'App\Controller\OrderController@index');
    Router::post('/order', 'App\Controller\OrderController@store');

    Router::post('/order/ali/pay/web', 'App\Controller\AliPayController@store');

}, ['middleware' => $authMiddleWare]);

//
Router::addGroup('/center', function ()
{
    //Admin
    Router::addGroup('/admin', function ()
    {
        Router::get('', 'App\Controller\Center\AdminController@show');
        Router::post('', 'App\Controller\Center\AdminController@store');
        Router::patch('', 'App\Controller\Center\AdminController@update');
        Router::delete('', 'App\Controller\Center\AdminController@delete');
        Router::patch('/status', 'App\Controller\Center\AdminController@disable');
        Router::patch('/password', 'App\Controller\Center\AdminController@resetPassword');
        Router::patch('/role', 'App\Controller\Center\AdminController@AssigningRole');
    });

    //Permission
    Router::addGroup('/permission', function ()
    {
        Router::get('', 'App\Controller\Center\PermissionController@show');
        Router::post('', 'App\Controller\Center\PermissionController@store');
        Router::patch('', 'App\Controller\Center\PermissionController@update');
        Router::delete('', 'App\Controller\Center\PermissionController@delete');
    });

    //Role
    Router::addGroup('/role', function ()
    {
        Router::get('', 'App\Controller\Center\RoleController@show');
        Router::post('', 'App\Controller\Center\RoleController@store');
        Router::patch('', 'App\Controller\Center\RoleController@update');
        Router::delete('', 'App\Controller\Center\RoleController@delete');
        Router::patch('/permission', 'App\Controller\Center\RoleController@assigningPermission');
    });

    //Product
    Router::addGroup('/product', function ()
    {
        Router::get('', 'App\Controller\Product\ProductController@index');

        Router::post('', 'App\Controller\Product\ProductController@store');
        Router::patch('', 'App\Controller\Product\ProductController@update');
        Router::delete('', 'App\Controller\Product\ProductController@delete');

        Router::addGroup('/sku', function ()
        {
            Router::post('', 'App\Controller\Product\ProductSkuController@store');
            Router::patch('', 'App\Controller\Product\ProductSkuController@update');
            Router::delete('', 'App\Controller\Product\ProductSkuController@delete');
        });
    });

}, ['middleware' => [
    JwtAuthMiddleWare::class,
    PermissionMiddleware::class,
]]);