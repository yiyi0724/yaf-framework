<?php

/**
 * 验证码逻辑类
 * @author enychen
 */
namespace image;

use \storage\SessionService;

class CaptchaService {

	/**
	 * 保存验证码
	 * @static
	 * @return boolean
	 */
	public static function save($channel, $code) {
		return SessionService::set($channel, $code);
	}

	/**
	 * 验证码比较
	 * @static
	 * @param string $key 键名
	 * @param string $code 用户输入的值
	 * @return boolean
	 */
	public static function compare($key, $code) {
		$sessionCode = SessionService::getAndDel($key);
		return !(empty($sessionCode) || strcasecmp($sessionCode, $code));
	}
}