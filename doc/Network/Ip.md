## 获取ip
[源码地址](https://github.com/enychen/yaf-framework/blob/master/app/library/Network/Ip.php)

### 完整案例
```php
$ip = \Network\Ip::get($ip2long = TRUE);
```

### IP类内置函数

###### 获取ip地址
```php
/**
 * @param boolean $ip2long 是否将ip转成整数
 * @return string|int
 */
\Network\Ip::get($ip2long = TRUE)
```
