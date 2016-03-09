## Http请求类（基于curl扩展）
[源码地址](https://github.com/enychen/yaf-framework/blob/master/app/library/Network/Http.php)

#### 完整案例
```php
try
{
  $params = array('param1'=>1, 'param2'=>2);

  // 创建对象
  $http = new \Network\Http($timeout = 60, $return = TRUE, $header = FALSE);
  $http->setAction("http://www.enychen.com/api/");
  $http->setDecode(\Network\Http::DECODE_JSON);
  $http->setCookie(array('author'=>'enychen', 'time'=>'2016-03-09'));
  $params['file'] = $http->getFile('/home/eny/Picture/1.jpg');
  $result = $http->upload($params);
  $http->close();
}
catch(\Exception  $e)
{
  $code = $e->getCode();
  $error = $e->getMessage();
}
```

#### 内置方法介绍
###### 创建一个http对象
```php
/**
 * @param int  $timeout 超时时间，默认60秒
 * @param bool $return  结果是否返回，如果不返回则直接输出，默认返回不输出
 * @param bool $header　启用时会将头文件的信息作为数据流输出, 默认不输出
 * @return void
 */
 $http = new \Network\Http(int $timeout = 60, bool $return = TRUE, bool $header = FALSE);
```

###### 设置请求的地址
```php
/**
 * @param string $action 请求地址
 * @return void
 */
$http->setAction(string $action)
```

###### 返回结果进行解析
```php
/**
 * @param string $decode 只支持 \Network\Http::DECODE_JSON 或者 \Network\Http::DECODE_XML
 * @return void
 */ 
$http->setDecode(int \Network\Http::DECODE_JSON);
```

###### 设置cookie信息
```php
/**
 * @param string|array $cookie cookie信息
 * @return void
 */
$http->setCookie(mixed $cookie);
```

###### 设置curlopt选项
```php
/**
 * @param string $key CURLOPT_*设置选项,参照http://php.net/manual/zh/function.curl-setopt.php
 * @param int|string|bool $value CURL选项值
 * @return void
 */
$http->setCurlopt(int $key, mixed $value)
```

###### 	上传文件的创建方式

> 由于php上传存在版本问题，所以封装了这个方法，需要主动调用此方法
```php
/**
 * @param string $path 文件的绝对路径
 * @return \CURLFile|string 上传文件对象或者字符串
 */
$http->getFile(string $path);
```

###### 关闭curl资源
```php
/**
 * @return void
 */
$http->close();
```
