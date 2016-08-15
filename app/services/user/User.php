<?php

/**
 * 用户逻辑基类
 */
namespace services\user;

use \services\base\Base as BaseService;

class User extends BaseService {

	/**
	 * 加密密钥
	 * @var string
	 */
	const PASSWORD_SECRET = 'p&fH^sdfb%NXuYmJ&@.+%$@126aERFdv';

	/**
	 * 获取加密的密码
	 * @param string $password 加密后的密码
	 * @return string
	 */
	public function getEnctypePassword($password) {
		return md5(sprintf("%s%s", sha1(md5(sprintf("%s%s", $password, time()), TRUE)), static::PASSWORD_SECRET));
	}
}