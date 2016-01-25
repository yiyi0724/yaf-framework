# 目录
1. [mysql](https://github.com/enychen/yaf-framework/tree/master/application/library/Driver#mysql)
  - [mysql内置方法](https://github.com/enychen/yaf-framework/blob/master/application/library/Driver/README.md#mysql内置方法)
    - 执行查询
    - 开启事务
    - 是不是在事务内
    - 提交事务
    - 回滚事务
    - 获取上次插入的id
    - 获取影响的行数
    - select获取所有
    - select获取一行
    - select获取一个值
2. [redis](https://github.com/enychen/yaf-framework/tree/master/application/library/Driver#redis)

## mysql
```php

// 数据库选项
$driver = array(
  'host'=>'127.0.0.1',
  'port'=>3306,
  'dbname'=>'test',
  'username'=>'root',
  'password'=>123456,
  'charset'=>'utf8',
);
$mysql = \Driver\Mysql::getInstance($driver);

```

#### mysql内置方法：
执行查询：
```php
// 如果先define('DEBUG_SQL', TRUE)，则下面语句输出调试信息，不执行
$mysql->query(string $sql, array $params = array());
```
开启事务:
```php
$mysql->beginTransaction();
```
是不是在事务内:
```php
$mysql->inTransaction();
```
提交事务:
```php
$mysql->commit();
```
回滚事务:
```php
$mysql->rollback();
```
获取上次插入的id:
```php
$mysql->lastInsertId();
```
获取影响的行数:
```php
$mysql->rowCount();
```
select获取所有:
```php
$mysql->fetchAll();
```
select获取一行:
```php
$mysql->fetch();
```
select获取一个值:
```php
$mysql->fetchColumn();
```


## redis
```php
$driver = array(
  'host'=>'127.0.0.1',
  'port'=>6379,
  'db'=>0,
  'timeout'=>30,
  'auth'=>NULL,
  'options'=>array('prefix'=>NULL),
);

// 说明点：
// options数组的每个key，比如常量完整写法是\Redis::OPT_PREFIX,则只要写prefix就行，其他配置以此类推

$redis = \Driver\Redis::getInstance($driver);
```

#### redis的方法，请参考[phpredis手册](https://github.com/phpredis/phpredis)
```php
// 简单例子
$redis->set('name', 'enychen');
$redis->get('name')
```
