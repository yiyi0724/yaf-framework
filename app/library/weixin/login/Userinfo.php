<?php
/**
 * 获取用户的access_token
 */
namespace \weixin\user;

class Userinfo extends Base {

	/**
	 * 用户access_token获取api
	 * @var string
	 */
	const USER_ACCESS_TOKEN_API = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid=%s&secret=%s&code=%s&grant_type=authorization_code';

	/**
	 * 刷新access_token接口
	 * @var string
	 */
	const USER_REFRESH_TOKEN_API = 'https://api.weixin.qq.com/sns/oauth2/refresh_token?appid=%s&grant_type=refresh_token&refresh_token=%s';

	/**
	 * 获取用户的信息接口
	 * @var string
	 */
	const USER_USERINFO = 'https://api.weixin.qq.com/sns/userinfo?access_token=%s&openid=%s&lang=%s';
	
	/**
	 * 用户验证后返回的code码
	 * @var string
	 */
	protected $code = NULL;

	/**
	 * 用户的授权token
	 * @var string
	 */
	protected $userAccessToken = NULL;

	/**
	 * token过期时间
	 * @var string
	 */
	protected $expire = NULL;

	/**
	 * token刷新密钥
	 * @var string
	 */
	protected $refreshToken = NULL; 

	/**
	 * 用户在公众号的唯一id
	 * @var string
	 */
	protected $openid = NULL;

	/**
	 * 登录作用域
	 * @var string
	 */
	protected $scope = NULL;

	/**
	 * 设置code码
	 * @param string $code code码
	 * @return Userinfo $this 返回当前对象进行连贯操作
	 */
	public function setCode($code) {
		$this->code = $code;
		$this->setUserAccessToken();
		return $this;
	}

	public function getCode() {
		return $this->code;
	}

	/**
	 * 设置用户的access_token等信息
	 * @return Userinfo $this 返回当前对象进行连贯操作
	 */
	protected function setUserAccessToken(){
		// 获取用户的access_token
		$result = json_decode($this->get(sprintf(self::USER_ACCESS_TOKEN_API, $this->getAppid(), $this->getAppSecret(), $this->getCode())));
		if(isset($result->errcode)){
			$this->throws(1000094, "{$result->errmsg}({$result->errcode})");
		}
		// 保存回调信息
		$this->setUserinfo($result);

		return $this;
	}

	/**
	 * 获取用户的access_token
	 * @return string
	 */
	protected function getUserAccessToken() {
		if($this->expire < time() && $this->refreshToken) {
			$result = json_decode($this->get(sprintf(self::USER_REFRESH_TOKEN_API, $this->getAppid(), $this->refreshToken)));
			if(isset($result->errcode)){
				$this->throws(1000094, "{$result->errmsg}({$result->errcode})");
			}
			$this->setUserinfo($result);
		}
		return $this->userAccessToken;
	}

	/**
	 * 保存获取access_token后的所有信息
	 * @param \stdClass $result curl后返回的对象
	 * @return void
	 */
	protected function setUserinfo($result) {
		$this->userAccessToken = $result->access_token;
		$this->expire = $result->expires_in + time();
		$this->refreshToken = $result->refresh_token;
		$this->openid = $result->openid;
		$this->scope = $result->scope;
	}

	public function getScope() {
		return $this->scope;
	}

	public function getOpenid() {
		return $this->openid;
	}

	/**
	 * 获取用户的具体信息（当scope为snsapi_userinfo的时候才可以获取）
	 * @param string $language 国家地区语言版本，zh_CN 简体，zh_TW 繁体，en 英语
	 * @return array 用户信息 openid | nickname | sex | province | city | country | headimgurl | privilege | unionid
	 * @throws \Exception
	 */
	public function getUserinfo($language = 'zh-CN') {
		if(!$this->getUserAccessToken()) {
			$this->throws(1000095, '请先进行获取用户令牌操作');
		}
		if($this->getScope() != 'snsapi_userinfo') {
			$this->throws(1000096, '获取用户信息权限不足');
		}

		$url = sprintf(self::USER_USERINFO, $this->getUserAccessToken(), $this->getOpenid(), $language);
		$result = json_decode($this->get($url), TRUE);
		if(isset($result['errcode'])) {
			$this->throws(1000097, "{$result['errmsg']}({$result['errcode']})");
		}

		return $result;
	}
}