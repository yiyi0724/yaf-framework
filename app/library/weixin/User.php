<?php

/**
 * 微信SDK用户类信息
 * @author enychen
 * @method
 */
namespace weixin;

class User extends Base {

	/**
	 * 获取的用户信息, 成功获取后, 将包含如下字段（snsapi_userinfo方式，如果是snsapi_base，则只有获取到access_token，openid）
	 * 	access_token	网页授权接口调用凭证,注意：此access_token与基础支持的access_token不同
	 * 	expires_in		access_token接口调用凭证超时时间，单位（秒）
	 * 	refresh_token	用户刷新access_token
	 * 	openid			用户唯一标识，请注意，在未关注公众号时，用户访问公众号的网页，也会产生一个用户和公众号唯一的OpenID
	 * 	scope			用户授权的作用域，使用逗号（,）分隔
	 * @var array
	 */
	protected $userAccessToken = NULL;

	/**
	 * 创建微信用户对象
	 * @param string $appid 公众号appid
	 * @param string $appSecret 公众号appSecret
	 * @param \storage\Adapter $storage 存储对象
	 */
	public function __construct($appid, $appSecret, \storage\Adapter $storage) {
		$this->setAppid($appid);
		$this->setAppSecret($appSecret);
		$this->setStorage($storage);
		$this->setAccessToken();
	}

	/**
	 * 设置获取用户的access_token
	 * @param array $userAccessToken
	 * @return void
	 */
	protected function saveUserAccessToken($userAccessToken) {
		$this->userAccessToken = $userAccessToken;
	}

	/**
	 * 用户跳转登录
	 * @param \weixin\user\Login $loginObject 登录对象
	 * @return void
	 * @throws \Exception
	 */
	public function authLogin(\weixin\user\Login $loginObject) {
		// 必备参数
		if(!$loginObject->getRedirectUri()) {
			$this->throws(1200, '请设置回调地址');
		}
		if(!$loginObject->getScope() || !in_array($loginObject->getScope(), array('snsapi_userinfo', 'snsapi_base', 'snsapi_login'))) {
			$this->throws(1201, 'scope不正确');
		}
		if(!$loginObject->getState()) {
			$this->throws(1202, '请设置state');
		}

		// api选择
		if($loginObject->getScope() == 'snsapi_log') {
			// 网页扫码登录
			$api = 'https://open.weixin.qq.com/connect/qrconnect?appid=%s&redirect_uri=%s&response_type=code&scope=%s&state=%s#wechat_redirect';
		} else {
			// 微信h5网站授权登录
			$api = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=%s&redirect_uri=%s&response_type=code&scope=%s&state=%s#wechat_redirect';
		}

		$url = sprintf($api, $this->getAppid(), $loginObject->getRedirectUri(), $loginObject->getScope(), $loginObject->getState());
		header("Location: {$url}");
		exit();
	}

	/**
	 * 获取用户的access_token
	 * @param string $code 用户跳转授权后回调附带的参数
	 * @return array
	 */
	public function getUserAccessToken($code){
		// 获取用户的access_token
		$api = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid=%s&secret=%s&code=%s&grant_type=authorization_code';	
		$result = json_decode($this->get(sprintf($api, $this->getAppid(), $this->getAppSecret(), $code)), TRUE);
		if(isset($result['errcode'])){
			$this->throws(1, $result['errmsg']);
		}

		// 暂存请求信息
		$this->saveUserAccessToken($result);

		return $result;
	}

	/**
	 * 获取用户的具体信息（当scope为snsapi_userinfo的时候才可以获取）
	 * @param string $language 国家地区语言版本，zh_CN 简体，zh_TW 繁体，en 英语
	 * @return array 用户信息
	 * @throws \Exception
	 */
	public function getUserinfo($language = 'zh-CN') {
		if(!$this->userAccessToken) {
			$this->throws(1, '请先进行获取用户令牌操作');
		}
		if(empty($this->userAccessToken->scope) || ($this->info->userAccessToken->scope != 'snsapi_userinfo')) {
			$this->throws(1, '获取用户信息权限不足');
		}

		$api = 'https://api.weixin.qq.com/sns/userinfo?access_token=%s&openid=%s&lang=%s';
		$url = sprintf($api, $this->userAccessToken->access_token, $this->userAccessToken->openid, $language);
		$result = json_decode($this->get($url), TRUE);
		if(isset($result['errcode'])) {
			$this->throws(2, $result['errmsg']);
		}

		// 整合2个数据
		$result = array_merge($this->userAccessToken, $result);

		return $result;
	}
}