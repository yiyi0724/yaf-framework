<?php
/**
 * 微信用户登录
 * @access enychen
 */
namespace\weixin\user;

class Auth extends \weixin\Base {
	/**
	 * 登录信息
	 * @var array
	 */
	private $login = array();

	/**
	 * 登录信息
	 * @param string $appid 公众号id
	 */
	public function __construct($appid) {
		$this->setAppid($appid);
	}

	/**
	 * 设置权限，微信公众号支持
	 * 	snsapi_userinfo	获取用户信息,需用户授权
	 * 	snsapi_base		直接跳转无需授权,只拿到用户的open_id
	 * 	snsapi_login	网页扫码登录
	 * @param string $scope 权限
	 * @param unknown $scope
	 */
	public function setScope($scope) {
		$this->login['scope'] = $scope;
	}

	/**
	 * 设置csrf防御
	 * @param string $state scrf值
	 * @return void
	 */
	public function setState($state) {
		$this->login['state'] = $state;
	}

	/**
	 * 设置登录后的回调地址
	 * @param string $redirectUri
	 * @return void
	 */
	public function setRedirectUri($redirectUri) {
		$this->login['redirectUri'] = urlencode($redirectUri);
	}
	
	/**
	 * 执行登录
	 * @return void
	 */
	public function jumpScan() {
		
	}

	public function jsScan() {
		
	}

	/**
	 * 公众号跳转登录
	 */
	public function jumpMp() {
		$url = sprintf(\weixin\API::USER_AUTHCODE_LOGIN, $this->appid, urlencode($redirectUri), $scope, $state);
		header('location:' . $requestUrl);
		exit();
	}

	/**
	 * 公众号无需跳转获取基本信息
	 */
	public function baseMp() {

	}
}