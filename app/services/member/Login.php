<?php

/**
 * 用户基类
 * @author enychen
 */
namespace services\member;

class Login extends Base {

	/**
	 * 用户名
	 * @var string
	 */
	protected $username;

	/**
	 * 密码
	 * @var string
	 */
	protected $password;

	/**
	 * 是否记住登录
	 * @var bool
	 */
	protected $isRemember = TRUE;

	/**
	 * 设置账号
	 * @param string $username 用户名
	 * @return Login $this 返回当前对象进行连贯操作
	 */
	public function setUsername($username) {
		$this->username = $username;
		return $this;
	}

	/**
	 * 获取账号
	 * @return string
	 */
	public function getUsername() {
		return $this->username;
	}

	/**
	 * 设置密码
	 * @param string $password 密码
	 * @return Login $this 返回当前对象进行连贯操作
	 */
	public function setPassword($password) {
		$this->password = $password;
		return $this;
	}

	/**
	 * 获取密码
	 * @return string
	 */
	public function getPassword() {
		return $this->password;
	}

	/**
	 * 设置是否登录
	 * @param bool $isRemember 是否进行登录
	 * @return Login $this 返回当前对象进行连贯操作
	 */
	public function setIsRemember($isRemember) {
		$this->isRemember = (bool)$isRemember;
		return $this;
	}

	/**
	 * 获取是否登录
	 * @return string
	 */
	public function getIsRemember() {
		return $this->isRemember;
	}

	/**
	 * 本站登录
	 */
	public function local() {
	}

	/**
	 * 手机登录
	 */
	public function mobile() {
	}

	/**
	 * 第三方登录
	 */
	public function oauth() {
		
	}

	/**
	 * 是否记住登录（默认记住一个月）
	 * @return void
	 */
	protected function rememberLogin() {
		if(UID) {
			$config = $this->getConfig('cookie');
			$encrypt = \security\Encryption::encrypt(array('uid'=>UID), 'yyq');
			setcookie(self::COOKIE_REMEMBER_KEY, $encrypt, time() + 2592000, $config->path, 
				$config->domain, $config->secure, $config->httponly);
		}
	}
}