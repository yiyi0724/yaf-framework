# 目录
[Http请求](https://github.com/enychen/yaf-framework/edit/master/application/library/Network/README.md#Http请求)  

## Http请求

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

## 发送邮件
```php
$mail = new \Network\Mail(); // 创建一个邮件对象
$mail->isSMTP(); // 使用SMPT验证
$mail->Host = 'smtp.163.com'; // smtp服务器
$mail->SMTPAuth = true; // 是否验证
$mail->Username = 'smtp的账号';	// 账号
$mail->Password = 'smtp的密码'; // 密码
$mail->Port = 25; // smtp服务器的端口号
 
$mail->setFrom('发送者邮箱地址', '发送者名称'); // 邮件的发送者
$mail->addAddress('收件者邮箱',  '收件者名称'); // 邮件的接收者
$mail->addAttachment('附件地址'); // 增加附件		

$mail->Subject = '邮件标题'; // 邮件的标题
$mail->Body    = '邮件的内容'; // 邮件的内容
$mail->AltBody = '邮件备注'; // 邮件的备注

$mail->isHTML(true); // 内容使用html的方式

if(!$mail->send()) {
  echo '发送失败: ' . $mail->ErrorInfo;
} else {
  echo '发送成功';
}
```
