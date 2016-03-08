<?php
class ImageForm
{
	/**
	 * 验证码验证
	 * @static
	 * @return array
	 */
	public static function captcha()
	{
		return array(
			'c'=>array(NULL, 'in', TRUE, '频道有误', ['login']),
			'w'=>array(NULL, 'number', FALSE, '验证码图片长度有误', ['min'=>100], 100),
			'h'=>array(NULL, 'number', FALSE, '验证码图片宽度度有误', ['min'=>33], 33)
		);
	}
}