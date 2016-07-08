## PDO类使用说明
[源码地址](https://github.com/enychen/yaf-framework/blob/master/app/library/Driver/Mysql.php)

### 创建对象
```php
// 数据库选项
$type = 'mysql';
$host = '127.0.0.1';
$port = 3306;
$dbname = 'test';
$username = 'root';
$password = '123456';
$charset = utf8;
// 单例模式获取对象
$pdo = \database\PDO::getInstance($type, $host, $port, $dbname, $charset, $username, $password);
```

### 内置方法
- [执行sql语句](https://github.com/enychen/yaf-framework/blob/master/doc/Driver/Mysql.md#执行sql语句)  
- [调试sql语句并结束程序](https://github.com/enychen/yaf-framework/blob/master/doc/Driver/Mysql.md#调试sql语句并结束程序)  
- [开启事务](https://github.com/enychen/yaf-framework/blob/master/doc/Driver/Mysql.md#开启事务)  
- [是否已经开启过事务](https://github.com/enychen/yaf-framework/blob/master/doc/Driver/Mysql.md#是否已经开启过事务)  
- [提交事务](https://github.com/enychen/yaf-framework/blob/master/doc/Driver/Mysql.md#提交事务)  
- [回滚事务](https://github.com/enychen/yaf-framework/blob/master/doc/Driver/Mysql.md#回滚事务)
- [获取上次插入的id](https://github.com/enychen/yaf-framework/blob/master/doc/Driver/Mysql.md#获取上次插入的id)  
- [获取影响的行数](https://github.com/enychen/yaf-framework/blob/master/doc/Driver/Mysql.md#获取影响的行数)  
- [select获取所有](https://github.com/enychen/yaf-framework/blob/master/doc/Driver/Mysql.md#select获取所有)  
- [select获取一行](https://github.com/enychen/yaf-framework/blob/master/doc/Driver/Mysql.md#select获取一行)  
- [select获取一个值](https://github.com/enychen/yaf-framework/blob/master/doc/Driver/Mysql.md#select获取一个值)  


###### 执行sql语句
```php
/**
 * @param string $sql    要执行的sql语句
 * @param array  $params sql语句预绑定参数，默认是array()
 * @return \Driver\Mysql 返回当前对象
 */
$pdo->query($sql, $params = array());
```
###### 调试sql语句并结束程序
```php
/**
 * @param string $sql    要执行的sql语句
 * @param array  $params sql语句预绑定参数，默认是array()
 * @return void
 */
$pdo->debug($sql, $params = array());
```
###### 开启事务:
```php
/**
 * @return boolean 开启成功返回TRUE
 */
$pdo->beginTransaction();
```
###### 是否已经开启过事务:
```php
/**
 * @return boolean 在一个事务内返回TRUE
 */
$pdo->inTransaction();
```
###### 提交事务:
```php
/**
 * @return boolean 事务提交成功返回TRUE
 */
$pdo->commit();
```
###### 回滚事务:
```php
/**
 * @return boolean 事务回滚成功返回TRUE
 */
$pdo->rollback();
```
###### 获取上次插入的id:
```php
/**
 * @return int 返回新插入的id
 */
$pdo->lastInsertId();
```
###### 获取影响的行数:
```php
/**
 * @return int 返回影响的行数
 */
$pdo->rowCount();
```
###### select获取所有:
```php
/**
 * @return array 返回selec的所有行
 */
$pdo->fetchAll();
```
###### select获取一行:
```php
/**
 * @return array 返回selec的一行
 */
$pdo->fetch();
```
###### select获取一个值:
```php
/**
 * @return array 返回selec的一个值
 */
$pdo->fetchColumn();
```
