<?php

/**
 * 验证码逻辑类
 * @author enychen
 */
namespace services\common;

class Captcha extends \services\base\Base {

	/**
	 * 登录验证码键
	 * @var string
	 */
	const LOGIN_KEY = 'login';
	
	/**
	 * 保存验证码
	 * @param string $key 键名
	 * @param string  $value 值
	 * @return boolean
	 */
	public function set($key, $value) {
		return $this->getSession()->set($key, $value);
	}

	/**
	 * 验证码比较
	 * @param string $key 键名
	 * @param string $code 传递过来的code
	 * @return boolean
	 */
	public function compare($key, $code) {
		return !strcasecmp($this->getSession()->get($key), $code);
	}
}