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
    //获取分类菜单
    Router::get('/category', 'App\Controller\CategoryController@menu');
    //用户注册
    Router::post('/user', 'App\Controller\UserController@store');
    //获取用户token
    Router::post('/user/token', 'App\Controller\TokenController@store');
    //重置用户密码
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
    Router::get('/product', 'App\Controller\ProductController@productList');
    //商品详情
    Router::get('/product/{id}', 'App\Controller\ProductController@show');

    //支付宝网页支付前端回调
    Router::get('/ali/pay/web', 'App\Controller\AliPayController@aliPayReturn');
    //支付宝网页支付服务器回调
    Router::post('/ali/pay/web/service', 'App\Controller\AliPayController@aliPayNotify');
    //分期支付支付宝网页支付服务器回调
    Router::post('/ali/pay/installment/web/service', 'App\Controller\AliPayController@installmentAliPayNotify');

    //微信支付服务器回调
    Router::post('/wechat/pay/web/service', 'App\Controller\WeChatPayController@weChatPayNotify');
    //微信退款服务器回调
    Router::post('/wechat/pay/refund/service', 'App\Controller\WeChatPayController@refundNotify');
    //微信分期退款服务器回调
    Router::post('/wechat/installment/pay/refund/service', 'App\Controller\WeChatPayController@installmentRefundNotify');
    //检查优惠券
    Router::get('/couponCode', 'App\Controller\CouponCodeController@couponCodeStatus');
    //分期支付支付宝网页支付服务器回调
    Router::post('/wechat/pay/installment/web/service', 'App\Controller\WeChatPayController@installmentAliPayNotify');

});

//登陆用户访问路由
Router::addGroup('/me', function ()
{
    //获取用户详情
    Router::get('', 'App\Controller\UserController@show');
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
    //创建众筹订单
    Router::post('/order/crowdfunding', 'App\Controller\OrderController@crowdfunding');
    //支付支付宝订单
    Router::post('/order/{order_id}/ali/pay/web', 'App\Controller\AliPayController@store');
    //支付微信订单
    Router::post('/order/{order_id}/wechat/pay/web', 'App\Controller\WeChatPayController@store');
    //确认收货
    Router::patch('/order/{order_id}/logistic', 'App\Controller\OrderController@receivedGood');
    //评论商品
    Router::post('/order/{order_id}/review', 'App\Controller\OrderController@review');
    //申请退款
    Router::post('/order/{order_id}/refund', 'App\Controller\OrderController@applyRefund');

    //创建分期订单
    Router::post('/order/{order_id}/installment', 'App\Controller\InstallmentController@installment');
    //获取用户分期列表
    Router::get('/installment', 'App\Controller\InstallmentController@index');
    //获取用户分期详情
    Router::get('/installment/{id}', 'App\Controller\InstallmentController@show');
    //支付宝分期支付
    Router::post('/installment/{id}/ali/pay', 'App\Controller\AliPayController@installmentPay');
    //微信分期支付
    Router::post('/installment/{id}/wechat/pay', 'App\Controller\WeChatPayController@installmentPay');


}, ['middleware' => $authMiddleWare]);

//后台管理
Router::addGroup('/center', function ()
{
    //Admin
    Router::addGroup('/admin', function ()
    {
        //获取用户列表
        Router::get('', 'App\Controller\AdminController@index');
        //创建用户
        Router::post('', 'App\Controller\AdminController@store');
        //更新用户
        Router::patch('/{id}', 'App\Controller\AdminController@update');
        //删除用户
        Router::delete('/{id}', 'App\Controller\AdminController@delete');
        //更改用户禁用状态
        Router::patch('/{id}/status', 'App\Controller\AdminController@disable');
        //重置用户密码
        Router::patch('/{id}/password', 'App\Controller\AdminController@resetPassword');
        //为用户分配角色
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
        //查看角色所有权限
        Router::get('/{id}/permission', 'App\Controller\RoleController@rolePermissions');
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

    //Order
    Router::addGroup('/order', function ()
    {
        //订单列表
        Router::get('', 'App\Controller\OrderController@orderList');
        //订单详情
        Router::get('/{id}', 'App\Controller\OrderController@orderInfo');
        //订单发货
        Router::patch('/{id}/logistic', 'App\Controller\OrderController@sendOutGood');
        //退款处理
        Router::patch('/{id}/refund', 'App\Controller\OrderController@handleRefund');
    });

    //CouponCode
    Router::addGroup('/couponCode', function ()
    {
        //优惠券列表
        Router::get('', 'App\Controller\CouponCodeController@index');
        //优惠券详情
        Router::get('/{id}', 'App\Controller\CouponCodeController@show');
        //创建优惠券
        Router::post('', 'App\Controller\CouponCodeController@store');
        //更新优惠券
        Router::patch('/{id}', 'App\Controller\CouponCodeController@update');
        //删除优惠券
        Router::delete('/{id}', 'App\Controller\CouponCodeController@delete');
    });

    //Category
    Router::addGroup('/category', function ()
    {
        //分类列表
        Router::get('', 'App\Controller\CategoryController@index');
        //创建分类
        Router::post('', 'App\Controller\CategoryController@store');
        //更新分类
        Router::patch('/{id}', 'App\Controller\CategoryController@update');
        //删除分类
        Router::delete('/{id}', 'App\Controller\CategoryController@delete');
    });

    //Crowdfunding
    Router::addGroup('/crowdfunding', function ()
    {
        //众筹商品列表
        Router::get('', 'App\Controller\CrowdfundingController@index');
        //创建众筹商品
        Router::post('', 'App\Controller\CrowdfundingController@store');
        //更新众筹商品
        Router::patch('/{id}', 'App\Controller\CrowdfundingController@update');
        //删除众筹商品
        Router::delete('/{id}', 'App\Controller\CrowdfundingController@delete');
    });

}, ['middleware' => $adminMiddleWare]);