## Http请求: 1.0

```php
try
{
  // 创建对象
  $http = new \Network\Http($url, $decode = NULL, $return = TRUE, $header = FALSE);
  // $url    string 要请求的url地址
  // $decode int 是否对结果进行解析, json: \Network\Http::DECODE_JSON, xml: \Network\Http::DECODE_XML
  // $return bool 结果是否返回, 默认返回
  // $header bool 启用时会将头信息作为数据流输出, 默认禁用
  
  // 可选方法
  // $http->setCookie($cookie); // 设置cookie信息，key=value; key=value 或者 array('key'=>'value')
  // $http->setHeader($headers); // 设置header信息，是一个字符串 或者 array('xxx', 'xxx')的格式
  // $data['upload'] = $http->getFile(string 文件名); // 版本问题，上传文件返回一个可上传的数据
  // $http->setCurlOpt(CURLOPT_*, $value); // 设置CURLOPT选项
  
  // 执行请求
  $result = $http->$method(array 要传递的参数); // $mthod可以使用的方法: get | post | put | delete | upload
  
  // 获取成功后接下来操作
}
catch(\Exception  $e)
{
  // 请求发生错误
  $error = $e->getMessage();
}
```

## 获取ip：1.0
```php
$ip = \Network\Ip::get(); // 默认将ip转成整数，如果不转，请传入参数FALSE即可
```

## 页面跳转：1.0
```php
// 带HTTP_REFERE的get跳转方式
\Network\Location::get(string $url);

// 带HTTP_REFERE的post跳转方式
\Network\Location::post(string $url, array $data);

// http协议进行的跳转
\Network\Location::redirect($url, int $code=NULL); //如果要进行301,302,303,307跳转，则输入$code
```
