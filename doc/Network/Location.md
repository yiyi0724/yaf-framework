## 页面跳转
[源码地址](https://github.com/enychen/yaf-framework/blob/master/app/library/Network/Location.php)

#### 内置方法介绍
###### 头信息跳转（不带HTTP_REFERE信息）
```php
/**
 * @param string $url 跳转地址
 * @param int $code 跳转状态码，支持 301 302 303 307，默认NULL
 * @return void
 */
\Network\Location::redirect(string $url, int $code = NULL);
```

###### 带HTTP_REFERER的跳转的get方式页面跳转
```
/**
 * @param string $url 跳转地址
 * @return void
 */
\Network\Location::get(string $url);
```

###### 带HTTP_REFERER的post方式页面跳转
```php
/**
 * @param string $url 跳转地址
 * @param array $data 附加参数
 * @return void
 */
\Network\Location::post(string $url, array $data = array());
```
