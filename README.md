# 简介
这是一套基于hyperf商城resultApi  
系统已经实现模块  
 - 用户模块
 - 权限模块
 - 商品模块
 - 订单模块
 - 支付模块
 本项目所有功能参考 learnku 社区的两本课程  
 [Laravel 教程 - 电商实战](https://learnku.com/courses/laravel-shop/7.x)  
 [Laravel 教程 - 电商进阶](https://learnku.com/courses/ecommerce-advance/6.x)  
 开发  
 集成短信注册、邮箱注册、角色权限、支付宝网页支付、微信扫码支付、ElasticSearch商品搜索等一系列商城基础功能。  
 接口文档预览  
 https://documenter.getpostman.com/view/10893401/Szzj7dAo
 
#声明
这个项目是本人在工作之余学习hyperf时编写的，所以emmm...,这并不是一个经过考验的项目，但是可能会适合一些刚学习hyperf的同学参考。在抄功能的同时，项目中也使用了很多hyperf的基础功能和组件，所以有问题可以尽情提交lssues,共同学习。    
说下项目存在的问题
1.项目初期因为并不打算编写太多功能，所以并没有很好组织项目结构，没有抽离DAO层，项目Controller中会有很多对模型的直接操作。  
2.使用了第三方微信非协程组件，所以部分功能存在阻塞。  
3.有部分冗余的对象，例如支付    

# Requirements

Hyperf has some requirements for the system environment, it can only run under Linux and Mac environment, but due to the development of Docker virtualization technology, Docker for Windows can also be used as the running environment under Windows.

The various versions of Dockerfile have been prepared for you in the [hyperf\hyperf-docker](https://github.com/hyperf/hyperf-docker) project, or directly based on the already built [hyperf\hyperf](https://hub.docker.com/r/hyperf/hyperf) Image to run.

When you don't want to use Docker as the basis for your running environment, you need to make sure that your operating environment meets the following requirements:  

 - PHP >= 7.2
 - Swoole PHP extension >= 4.4，and Disabled `Short Name`
 - OpenSSL PHP extension
 - JSON PHP extension
 - PDO PHP extension （If you need to use MySQL Client）
 - Redis PHP extension （If you need to use Redis Client）
 - Protobuf PHP extension （If you need to use gRPC Server of Client）

# Installation using Composer

The easiest way to create a new Hyperf project is to use Composer. If you don't have it already installed, then please install as per the documentation.

To create your new Hyperf project:

$ composer create-project hyperf/hyperf-skeleton path/to/install

Once installed, you can run the server immediately using the command below.

$ cd path/to/install
$ php bin/hyperf.php start

This will start the cli-server on port `9501`, and bind it to all network interfaces. You can then visit the site at `http://localhost:9501/`

which will bring up Hyperf default home page.
