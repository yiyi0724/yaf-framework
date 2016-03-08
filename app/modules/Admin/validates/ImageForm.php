<?php
class ImageForm
{
	/**
	 * 登录验证
	 * @static
	 * @return array
	 */
	public static function captcha()
	{
		// 键名 来源 检查方法 是否必须 错误提示 可选项检查 别名
		return array(
			'channel'=>array(NULL, 'in', TRUE, '频道有误', ['login']),
		);
	}
}