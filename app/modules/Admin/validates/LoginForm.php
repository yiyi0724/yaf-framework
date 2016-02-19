<?php
class LoginForm
{
	/**
	 * 登录验证
	 * @static
	 * @return array
	 */
	public static function loginRules()
	{
		// 键名 来源 检查方法 是否必须 错误提示 可选项检查 别名
		return array(
			'username'=>array('POST', 'string', TRUE, '用户名不正确', ['min'=>8]), 
			'password'=>array('POST', 'string', TRUE, '密码长度不够', ['min'=>8])
		);
	}
}