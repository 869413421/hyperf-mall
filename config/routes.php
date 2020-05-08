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

$adminMiddleWare = [
    JwtAuthMiddleWare::class,
    PermissionMiddleware::class,
];

//游客访问路由
Router::addGroup('', function ()
{
    Router::get('/test', 'App\Controller\IndexController@test');
    //用户注册
    Router::post('/user', 'App\Controller\UserController@store');
    //获取用户token
    Router::post('/user/token', 'App\Controller\TokenController@store');
    //重置用户密码，TODO
    Router::patch('/user/password', 'App\Controller\UserController@resetPassword');
    //重发验证邮件
    Router::post('/user/identity/email', 'App\Controller\EmailController@sendVerifyEmail');
    //邮箱验证
    Router::get('/email/identity', 'App\Controller\EmailController@verifyEmail');

    //验证码
    Router::get('/captcha', 'App\Controller\CaptchaController@store');
    //发送短信验证码
    Router::post('/sms', 'App\Controller\SmsController@store');

    //商品列表
    Router::get('/product', 'App\Controller\ProductController@index');
    //商品详情
    Router::get('/product/{id}', 'App\Controller\ProductController@show');

    //支付宝网页支付前端回调
    Router::get('/ali/pay/web', 'App\Controller\AliPayController@aliPayReturn');
    //支付宝网页支付服务器回调
    Router::post('/ali/pay/web/service', 'App\Controller\AliPayController@aliPayNotify');

    //微信支付服务器回调
    Router::post('/wechat/pay/web/service', 'App\Controller\WeChatPayController@aliPayNotify');

});

//登陆用户访问路由
Router::addGroup('/me', function ()
{
    //更新资料
    Router::patch('', 'App\Controller\UserController@update');
    //上传头像
    Router::post('/avatar', 'App\Controller\FileController@uploadAvatar');
    //更新token
    Router::patch('/token', 'App\Controller\TokenController@update');
    //删除token退出
    Router::delete('/token', 'App\Controller\TokenController@delete');

    //收获地址列表
    Router::get('/addresses', 'App\Controller\UserAddressesController@show');
    //新建收获地址
    Router::post('/addresses', 'App\Controller\UserAddressesController@store');
    //更新收获地址
    Router::patch('/addresses/{id}', 'App\Controller\UserAddressesController@update');
    //删除收获地址
    Router::delete('/addresses/{id}', 'App\Controller\UserAddressesController@delete');

    //收藏商品列表
    Router::get('/product/collect', 'App\Controller\ProductController@favorites');
    //更新商品收藏
    Router::post('/product/collect/{id}', 'App\Controller\ProductController@favor');
    //删除商品收藏
    Router::delete('/product/collect/{id}', 'App\Controller\ProductController@detach');

    //购物车列表
    Router::get('/cart', 'App\Controller\CartController@index');
    //添加到购物车
    Router::post('/cart', 'App\Controller\CartController@store');
    //删除购物车
    Router::delete('/cart/{id}', 'App\Controller\CartController@delete');

    //获取订单列表
    Router::get('/order', 'App\Controller\OrderController@index');
    //创建新订单
    Router::post('/order', 'App\Controller\OrderController@store');
    //支付支付宝订单
    Router::post('/order/{order_id}/ali/pay/web', 'App\Controller\AliPayController@store');
    //支付微信订单
    Router::post('/order/{order_id}/wechat/pay/web', 'App\Controller\WeChatPayController@store');
}, ['middleware' => $authMiddleWare]);

//后台管理
Router::addGroup('/center', function ()
{
    //Admin
    Router::addGroup('/admin', function ()
    {
        //获取管理员列表
        Router::get('', 'App\Controller\AdminController@index');
        //创建管理员
        Router::post('', 'App\Controller\AdminController@store');
        //更新管理员
        Router::patch('/{id}', 'App\Controller\AdminController@update');
        //删除管理员
        Router::delete('/{id}', 'App\Controller\AdminController@delete');
        //更改管理员禁用状态
        Router::patch('/{id}/status', 'App\Controller\AdminController@disable');
        //重置管理员密码
        Router::patch('/{id}/password', 'App\Controller\AdminController@resetPassword');
        //为管理员分配角色
        Router::patch('/{id}/role/{role_id}', 'App\Controller\AdminController@AssigningRole');
    });

    //Permission
    Router::addGroup('/permission', function ()
    {
        //获取权限列表
        Router::get('', 'App\Controller\PermissionController@index');
        //创建权限
        Router::post('', 'App\Controller\PermissionController@store');
        //更新权限
        Router::patch('/{id}', 'App\Controller\PermissionController@update');
        //删除权限
        Router::delete('/{id}', 'App\Controller\PermissionController@delete');
    });

    //Role
    Router::addGroup('/role', function ()
    {
        //获取角色列表
        Router::get('', 'App\Controller\RoleController@index');
        //创建角色
        Router::post('', 'App\Controller\RoleController@store');
        //更新角色
        Router::patch('/{id}', 'App\Controller\RoleController@update');
        //删除角色
        Router::delete('/{id}', 'App\Controller\RoleController@delete');
        //为角色分配权限
        Router::patch('/{id}/permission', 'App\Controller\RoleController@assigningPermission');
    });

    //Product
    Router::addGroup('/product', function ()
    {
        //获取商品列表
        Router::get('', 'App\Controller\ProductController@index');
        //创建商品
        Router::post('', 'App\Controller\ProductController@store');
        //更新商品
        Router::patch('/{id}', 'App\Controller\ProductController@update');
        //删除商品
        Router::delete('/{id}', 'App\Controller\ProductController@delete');
    });

    //ProductSku
    Router::addGroup('/product/{id}/sku', function ()
    {
        //新建库存
        Router::post('', 'App\Controller\ProductSkuController@store');
        //修改库存
        Router::patch('/{sku_id}', 'App\Controller\ProductSkuController@update');
        //删除库存
        Router::delete('/{sku_id}', 'App\Controller\ProductSkuController@delete');
    });

}, ['middleware' => $adminMiddleWare]);