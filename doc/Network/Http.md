## Http请求

```php
try
{
  // 创建对象
  $http = new \Network\Http($timeout = 60, $return = TRUE, $header = FALSE);
  $http
  
  // 可选方法
  // $http->setCookie($cookie); // 设置cookie信息，key=value; key=value 或者 array('key'=>'value')
  // 
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