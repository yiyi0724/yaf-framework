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
	 * oauth登录用户的来源
	 * @var string
	 */
	protected $from = NULL;

	/**
	 * oauth登录用户的唯一key
	 * @var string
	 */
	protected $openid = NULL;

	/**
	 * 微信登录的附加参数
	 * @var string
	 */
	protected $unionid = '';

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
	 * 设置用户的openid
	 * @param string $openid 唯一key
	 * @return Login $this 返回当前对象进行连贯操作
	 */
	public function setOpenid() {
		
	}

	/**
	 * 本站登录
	 */
	public function todo() {
	}

	protected function oauthLogin() {
		
	}

	protected function localLogin() {
		
	}
}