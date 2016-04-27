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

### 内置方法


###### 执行sql语句
```php
/**
 * @param string $sql    要执行的sql语句
 * @param array  $params sql语句预绑定参数，默认是array()
 * @return \Driver\Mysql 返回当前对象
 */
$mysql->query($sql, $params = array());
```
###### 调试sql语句并结束程序
```php
/**
 * @param string $sql    要执行的sql语句
 * @param array  $params sql语句预绑定参数，默认是array()
 * @return void
 */
$mysql->debug($sql, $params = array());
```
###### 开启事务:
```php
/**
 * @return boolean 开启成功返回TRUE
 */
$mysql->beginTransaction();
```
###### 是不是在事务内:
```php
/**
 * @return boolean 在一个事务内返回TRUE
 */
$mysql->inTransaction();
```
###### 提交事务:
```php
/**
 * @return boolean 事务提交成功返回TRUE
 */
$mysql->commit();
```
###### 回滚事务:
```php
/**
 * @return boolean 事务回滚成功返回TRUE
 */
$mysql->rollback();
```
###### 获取上次插入的id:
```php
/**
 * @return int 返回新插入的id
 */
$mysql->lastInsertId();
```
###### 获取影响的行数:
```php
/**
 * @return int 返回影响的行数
 */
$mysql->rowCount();
```
###### select获取所有:
```php
/**
 * @return array 返回selec的所有行
 */
$mysql->fetchAll();
```
###### select获取一行:
```php
/**
 * @return array 返回selec的一行
 */
$mysql->fetch();
```
###### select获取一个值:
```php
/**
 * @return array 返回selec的一个值
 */
$mysql->fetchColumn();
```
