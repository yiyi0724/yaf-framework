<?php
class LoginForm
{	
	/**
	 * 登录验证
	 * @static
	 * @return array
	 */
	public static function login()
	{
		return array(
			'username'=>array('POST', 'string', TRUE, '用户名不正确', ['min'=>5]), 
			'password'=>array('POST', 'string', TRUE, '密码长度不够', ['min'=>6]),
			'captcha'=>array('POST', 'string', TRUE, '验证码不正确'),
		);
	}
}