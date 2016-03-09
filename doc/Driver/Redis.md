## Redis类使用说明
[源码地址](https://github.com/enychen/yaf-framework/blob/master/app/library/Driver/Redis.php)

### 创建Redis对象

> options数组的每个key，比如常量完整写法是\Redis::OPT_PREFIX,则只要写prefix就行，其他配置以此类推

```php
$driver = array(
  'host'=>'127.0.0.1',
  'port'=>6379,
  'db'=>0,
  'timeout'=>30,
  'auth'=>NULL,
  'options'=>array('prefix'=>NULL),
);

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
