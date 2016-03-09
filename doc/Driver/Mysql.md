## Mysql类使用说明
[源码地址](https://github.com/enychen/yaf-framework/blob/master/app/library/Driver/Mysql.php)

### 创建mysql对象
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
// 单例模式获取对象
$mysql = \Driver\Mysql::getInstance($driver);
```

### Mysql类内置方法


###### 执行sql语句
```php
$mysql->query(string $sql, array $params = array());
```
###### 调试sql语句
```php
$mysql->debug(string $sql, array $params = array());
```
###### 开启事务:
```php
$mysql->beginTransaction();
```
###### 是不是在事务内:
```php
$mysql->inTransaction();
```
###### 提交事务:
```php
$mysql->commit();
```
###### 回滚事务:
```php
$mysql->rollback();
```
###### 获取上次插入的id:
```php
$mysql->lastInsertId();
```
###### 获取影响的行数:
```php
$mysql->rowCount();
```
###### select获取所有:
```php
$mysql->fetchAll();
```
###### select获取一行:
```php
$mysql->fetch();
```
###### select获取一个值:
```php
$mysql->fetchColumn();
```
