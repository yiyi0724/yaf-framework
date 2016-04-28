## 发送邮件
[源码地址_非原创](https://github.com/enychen/yaf-framework/blob/master/app/library/Network/Mail.php)

### 完整案例
```php
$mail = new \Network\Mail(); // 创建一个邮件对象
$mail->isSMTP(); // 使用SMPT验证
$mail->Host = 'smtp.163.com'; // smtp服务器，如163邮箱
$mail->SMTPAuth = true; // 是否验证
$mail->Username = 'smtp的账号';	// 账号
$mail->Password = 'smtp的密码'; // 密码
$mail->Port = 25; // smtp服务器的端口号
 
$mail->setFrom('发送者邮箱', '发送者称谓'); // 邮件的发送者信息
$mail->addAddress('收件者邮箱',  '收件者称谓'); // 邮件的接收者信息
$mail->addAttachment('附件地址'); // 增加附件		

$mail->Subject = '邮件标题'; // 邮件的标题
$mail->Body    = '邮件内容'; // 邮件的内容
$mail->AltBody = '邮件备注'; // 邮件的备注

$mail->isHTML(true); // 内容使用html的方式

if(!$mail->send()) {
  exit('发送失败: ' . $mail->ErrorInfo);
}
echo '发送成功';
```
