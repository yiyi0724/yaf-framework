<?php

/**
 * 验证码操作逻辑封装
 * @author enychen
 */
namespace logic;

class Captcha {
	/**
	 * 验证码前缀
	 * @var string
	 */
	const PREFIX = 'captcha.';

	/**
	 * 把验证码写到session中
	 * @param string $channel 验证码频道
	 * @param string $code 验证码
	 * @return void
	 */
	public static function set($channel, $code) {
		$session = \Yaf\Session::getInstance();
		$session->set(static::PREFIX . $channel, $code);
		$session->set(static::PREFIX . "{$channel}.limit", 0);
	}

	/**
	 * 检查登录验证码是否正确
	 * @param string $channel 验证码频道
	 * @param string $code 验证码
	 * @param string $limit 最多验证几次
	 * @return bool
	 */
	public static function check($channel, $code, $limit = 1) {
		$session = \Yaf\Session::getInstance();
		$sessionCode = $session->get(static::PREFIX . $channel);
		if(strcasecmp($code, $sessionCode)) {
			$sessionLimit = $session->get(static::PREFIX . "{$channel}.limit");
			if($sessionLimit == $limit) {
				$session->del(static::PREFIX . $channel);
			} else {
				$session->set(static::PREFIX . "{$channel}.limit", $limit+1);
			}
			return FALSE;
		} else {
			$session->del(static::PREFIX . $channel);
			return TRUE;
		}
	}
}