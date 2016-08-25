<?php

/**
 * 微信用户登录认证
 * @access enychen
 */
namespace wxsdk\login;

class Oauth extends Base {

	/**
	 * 二维码扫描登录接口
	 * @var string
	 */
	const QRCODE_API = 'https://open.weixin.qq.com/connect/qrconnect?appid=%s&redirect_uri=%s&response_type=code&scope=%s&state=%s#wechat_redirect';

	/**
	 * 微信app内部登录接口
	 * @var string
	 */
	const OAUTH_API = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=%s&redirect_uri=%s&response_type=code&scope=%s&state=%s#wechat_redirect';

	/**
	 * 回调地址
	 * @var string
	 */
	protected $redirectUri = NULL;

	/**
	 * 作用域
	 * @var string
	 */
	protected $scope = NULL;

	/**
	 * csrf防御值
	 * @var string
	 */
	protected $state = NULL;

	/**
	 * 构造函数
	 * @param string $appid 公众号唯一凭证，不传默认为：WEIXIN_APPID
	 */	
	public function __construct($appid = NULL) {
		parent::__construct($appid, NULL);
	}

	/**
	 * 设置作用域，必须
	 * 	snsapi_userinfo	获取用户信息,需用户授权
	 * 	snsapi_base		直接跳转无需授权,只拿到用户的open_id
	 * 	snsapi_login	网页扫码登录
	 * @param string $scope 作用域名称
	 * @return Base $this 返回当前对象进行连贯操作
	 */
	public function setScope($scope) {
		$this->scope = $scope;
		return $this;
	}

	/**
	 * 获取作用域
	 * @return string
	 */
	public function getScope() {
		return $this->scope;
	}

	/**
	 * 设置csrf防御值
	 * @param string $state scrf值
	 * @return Base $this 返回当前对象进行连贯操作
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
	 * 设置登录回调地址（内部会进行urlencode），必须
	 * @param string $redirectUri url地址
	 * @return Base $this 返回当前对象进行连贯操作
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

	/**
	 * 扫描二维码登录
	 * @return void
	 * @throws \wxsdk\WxException
	 */
	public function scanQrcode() {
		// 必传参数
		if(!$this->getRedirectUri()) {
			$this->throws(100021, '请设置回调地址');
		}
		if(!$this->getState()) {
			$this->throws(100022, '请设置state');
		}

		// 进行跳转
		$this->setScope('snsapi_login');
		$url = sprintf(self::QRCODE_API, $this->getAppid(), $this->getRedirectUri(), $this->getScope(), $this->getState());
		header("Location: {$url}");
		exit();
	}

	/**
	 * 微信app登录
	 * @return void
	 * @throws \wxsdk\WxException
	 */
	public function oauth() {
		// 必传参数
		if(!$this->getRedirectUri()) {
			$this->throws(100021, '请设置回调地址');
		}
		if(!$this->getState()) {
			$this->throws(100022, '请设置state');
		}
		if(!in_array($this->getScope(), array('snsapi_userinfo', 'snsapi_base'))) {
			$this->throws(100023, '请设置作用域');
		}

		// 进行跳转
		$url = sprintf(self::OAUTH_API, $this->getAppid(), $this->getRedirectUri(), $this->getScope(), $this->getState());
		header("Location: {$url}");
		exit();
	}
}