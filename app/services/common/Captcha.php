<?php

/**
 * 验证码逻辑类
 * @author enychen
 */
namespace services\common;

use \services\base\Base as BaseService;
use \image\Captcha as CaptchaLib;

class Captcha extends BaseService {

	/**
	 * 保存验证码
	 * @return boolean
	 */
	public static function save($channel, $code) {
		return self::getSession()->set($channel, $code);
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