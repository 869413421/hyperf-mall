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
 
 集成短信注册、邮箱注册、角色权限、支付宝网页支付、微信扫码支付、分期支付、众筹、无限级分类、秒杀、ElasticSearch商品分面搜索等一系列商城基础功能。  
 接口文档预览  
 https://documenter.getpostman.com/view/10893401/Szzj7dAo
 
# 声明  

这个项目是本人在工作之余学习hyperf时编写的，所以emmm...,这并不是一个经过考验的项目，但是可能会适合一些刚学习hyperf的同学参考。在抄功能的同时，项目中也使用了很多hyperf的基础功能和组件，所以有问题可以尽情提交lssues,共同学习。     
说下项目存在的问题  

1.项目初期因为并不打算编写太多功能，所以并没有很好组织项目结构，没有抽离DAO层，项目Controller中会有很多对模型的直接操作。  

2.使用了第三方微信非协程组件，所以部分功能存在阻塞。  

3.有部分冗余的对象，例如支付   

4.因为微信支付测试门槛过高，微信支付尚未测试

# 系统要求

 - PHP >= 7.2
 - Swoole PHP 扩展 >= 4.4 并且关闭 `Short Name`
 - OpenSSL PHP 扩展
 - JSON PHP 拓展
 - PDO PHP 拓展 
 - Redis PHP 拓展 
 - Protobuf PHP 拓展
 - ElasticSearch >= 7.0

# 安装

获取代码  
` git clone https://github.com/869413421/HyperfMall.git`

安装组件  
`composer install`

执行数据库迁移  
`php bin/hyperf.php migrate --seed`

执行ElasticSearch索引迁移  
`php bin/hyperf.php es:migrate`

执行同步商品到ElasticSearch命令  
`php bin/hyperf.php es:migrate`

启动  
`php watch`

