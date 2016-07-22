<?php

/**
 * 左侧栏目逻辑
 * @author enychen
 */
namespace services\common;

class Security extends \services\base\Base {

	/**
	 * 登录验证码
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
}