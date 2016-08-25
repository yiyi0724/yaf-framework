<?php

/**
 * 验证码逻辑类
 * @author enychen
 */
namespace services\common;

use \services\base\Base;
use \image\Captcha as CaptchaLib;

class Captcha extends Base {

	/**
	 * 生成验证码图片并将验证码值保存到session
	 * @return boolean
	 */
	public static function create($key) {
		$captcha = new CaptchaLib();
		$captcha->setCanvasBgColor(55, 62, 74)->show();
		return self::getSession()->set($key, $captcha->getCode());
	}

	/**
	 * 验证码比较
	 * @param string $key 键名
	 * @param string $code 用户输入的值
	 * @return boolean
	 */
	public static function compare($key, $code) {
		$session = self::getSession();
		$isMatch = !strcasecmp($session->get($key), $code);
		$session->del($key);
		return $isMatch;
	}
}