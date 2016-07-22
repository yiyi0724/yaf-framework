<?php
class ImageForm {
	
	/**
	 * 登录验证
	 * @static
	 * @return array
	 */
	public static function captchaAction() {
		return array(
			'channel'=>array('GET', 'in', TRUE, '验证码渠道类型有误', ['login']),
		);
	}
}