<?php

class IndexController extends BaseController
{
	public function indexAction()
	{
		$page = $this->getRequest()->get('page', 1);
		$limit = 10;		
		$offset = ($page-1)*$limit;
		
		// 当前页的条数
		$lists = $this->getDb()->table('loto_userbets')->limit($offset, $limit)->select();
		
		// 数据库一共有几条
		$count = $this->getDb()->field('count("id")')->table('loto_userbets')->select('fetchColumn');
		
		$page = new \Html\Page($count, $limit, 10);
		$page->create();
		$page->output();
		
		exit;		
	}
	
	public function getDb() {
		$driver['host'] = '127.0.0.1';
		$driver['port'] = 3306;
		$driver['dbname'] = 'my5755';
		$driver['username'] = 'root';
		$driver['password'] = '123456';
		$driver['charset'] = 'utf8';
		return \Driver\Mysql::getInstance($driver);
	}
	
	public function sendmailAction()
	{		
		$mail = new \Network\Mail();			// 创建一个邮件对象

		$mail->isSMTP(); 							// 使用SMPT验证
		$mail->Host = 'smtp.163.com';				// smtp服务器
		$mail->SMTPAuth = true;                    	// 是否验证
		$mail->Username = '15959375069@163.com';	// 账号
		$mail->Password = 'qvbdfzboatoxbrcp'; 		// 密码
		$mail->Port = 25;                           // smtp服务器的端口号
		
		$mail->setFrom('15959375069@163.com', '陈晓波');		// 邮件的发送者
		$mail->addAddress('346745114@qq.com',  'enychen');		// 邮件的接收者
		//$mail->addReplyTo('15959375069@163.com', '陈晓波');	// 增加邮件的恢复标签,好像没什么用啊
		$mail->addAttachment('/home/eny/Downloads/ip.log');     // 增加附件		
		
		$mail->Subject = '我在测试发送邮件';			// 邮件的标题
		$mail->Body    = '甩卖妹子一枚, <b>yyq!</b>';	// 邮件的内容
		$mail->AltBody = '为了两块钱我也是拼啊';		// 邮件的备注
		
		$mail->isHTML(true);                        // 内容使用html的方式

		if(!$mail->send()) {
			echo '发送失败: ' . $mail->ErrorInfo;
		} else {
			echo '发送成功';
		}
		
		exit;
	}
}