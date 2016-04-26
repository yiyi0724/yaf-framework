<?php

/**
 * 网站默认控制器
 * @author enychen
 *
 */
class IndexController extends \Base\WwwController {

	/**
	 * 网站首页
	 */
	public function indexAction() {
		$mail = new \Network\Mail();
		
		$mail = new \Network\Mail(); // 创建一个邮件对象
		$mail->isSMTP(); // 使用SMPT验证
		$mail->Host = 'smtp.qq.com'; // smtp服务器
		$mail->SMTPAuth = true; // 是否验证
		$mail->Username = '285577011@qq.com';  // 账号
		$mail->Password = 'axzwjwueuaasbiig'; // 密码
		$mail->Port = 25; // smtp服务器的端口号
		
		$mail->setFrom('285577011@qq.com', '对方不理你并想你扔了一个杨艳琴'); // 邮件的发送者
		$mail->addAddress('89932976@qq.com',  '对方'); // 邮件的接收者
		//$mail->addAttachment('附件地址'); // 增加附件
		
		$mail->Subject = '黄燕雨傻逼'; // 邮件的标题
		$mail->Body    = '邮件的内容'; // 邮件的内容
		$mail->AltBody = '邮件备注'; // 邮件的备注
		
		$mail->isHTML(true); // 内容使用html的方式
		
		if($mail->send()) {
			echo '发送成功';
		} else {
			echo '发送失败: ' . $mail->ErrorInfo;
		}
		
		exit;
	}
}