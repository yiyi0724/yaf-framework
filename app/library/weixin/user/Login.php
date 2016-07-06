<?php

/**
 * 微信用户登录认证
 * @access enychen
 */
namespace weixin\user;

class Login {

	/**
	 * 回调地址
	 * @var string
	 */
	private $redirectUri = NULL;

	/**
	 * 获取用户信息的作用域
	 * @var string
	 */
	private $scope = NULL;

	/**
	 * csrf防御值
	 * @var string
	 */
	private $state = NULL;

	/**
	 * 设置权限，微信公众号支持
	 * 	snsapi_userinfo	获取用户信息,需用户授权
	 * 	snsapi_base		直接跳转无需授权,只拿到用户的open_id
	 * 	snsapi_login	网页扫码登录
	 * @param string $scope 权限
	 * @return Login $this 返回当前对象进行连贯操作
	 */
	public function setScope($scope) {
		$this->scope = $scope;
		return $this;
	}

	/**
	 * 获取登录权限
	 * @return string
	 */
	public function getScope() {
		return $this->scope;
	}

	/**
	 * 设置csrf防御值
	 * @param string $state scrf值
	 * @return Login $this 返回当前对象进行连贯操作
	 */
	public function setState($state) {
		$this->state = $state;
		return $this;
	}

	/**
	 * 获取csrf防御值
	 * @return string
	 */
	public function getState() {
		return $this->state;
	}

	/**
	 * 设置登录回调地址（内部会进行urlencode）
	 * @param string $redirectUri
	 * @return Login $this 返回当前对象进行连贯操作
	 */
	public function setRedirectUri($redirectUri) {
		$this->redirectUri = urlencode($redirectUri);
		return $this;
	}

	/**
	 * 获取登录回调地址
	 * @return string
	 */
	public function getRedirectUri() {
		return $this->redirectUri;
	}
}