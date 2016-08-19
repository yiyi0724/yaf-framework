<?php

/**
 * 用户逻辑基类
 * @author enychen
 */
namespace services\user;

class Base extends \services\base\Base {

	/**
	 * 加密密钥
	 * @var string
	 */
	const PASSWORD_SECRET = 'p&fH^sdfb%NXuY95867235mJ&@.+%$@126aERFdv';

	/**
	 * 获取加密的密码
	 * @param string $password 加密后的密码
	 * @return string
	 */
	public function getEnctypePassword($password) {
		return md5(sprintf("%s%s", sha1(md5(sprintf("%s%s", $password, time()), TRUE)), static::PASSWORD_SECRET));
	}
}