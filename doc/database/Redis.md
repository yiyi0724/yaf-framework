## Redis类使用说明
[源码地址](https://github.com/enychen/yaf-framework/blob/master/app/library/Driver/Redis.php)

### 创建Redis对象

> options数组的每个key，比如常量完整写法是\Redis::OPT_PREFIX,则只要写prefix就行，其他配置以此类推

```php
// redis 配置,全部必须
$driver = array(
  'host'=>'127.0.0.1',
  'port'=>6379,
  'dbname'=>0,
  'timeout'=>30,
  'auth'=>NULL,
  'options'=>array('prefix'=>NULL),
);
// 单例模式获取对象
$redis = \Driver\Redis::getInstance($driver);
```


### redis的方法
[phpredis手册](https://github.com/phpredis/phpredis)

###### 简单例子
```php
// 简单例子
$redis->set('name', 'enychen');
$redis->get('name')
```