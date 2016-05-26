<?php

/**
 * 微信SDK用户类
 * @author enychen
 * @method
 * getUserAuthCode 			跳转到授权页面获取用户的code信息
 * getUserAccessToken 		获取用户的令牌
 * refreshUserAccessToken 	刷新用户的令牌
 * 
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
	 * @var \stdClass
	 */
	protected $info = NULL;

	/**
	 * 跳转到用户授权页面，让用户进行授权
	 * @param string $redirectUri 回调的地址, 无需urlencode后再传递
	 * @param string $scope 授权方式: snsapi_userinfo|获取用户信息,需用户授权;  snsapi_base|直接跳转无需授权,只拿到用户的open_id
	 * @param string $state 防止csrf
	 * @return void
	 */
	public function getUserAuthCode($redirectUri, $scope, $state) {
		$url = sprintf(API::GET_USER_CODE, $this->appid, urlencode($redirectUri), $scope, $state);
		header('location:' . $requestUrl);
		exit();
	}

	/**
	 * 获取用户网页授权access_token
	 * @param string $code 通过getUserAuthCode方法获取的结果code
	 * @return void
	 * @throws \Exception
	 */
	public function getUserAccessToken($code) {
		$url = sprintf(API::GET_UESR_ACCESS_TOKEN, $this->appid, $this->appSecret, $code);
		$result = json_decode($this->get($url));
		if(isset($result->errcode)) {
			throw new \Exception($result->errmsg, $result->errcode);
		}
		
		$this->info = $result;
	}
	
	/**
	 * 刷新用户的access_token
	 * @return void
	 * @throws \Exception
	 */
	public function refreshUserAccessToken() {
		$url = sprintf(API::REFRESH_USER_ACCESS_TOKEN, $this->appid, $this->info->refresh_token);
		$result = json_decode($this->get($url));
		if(isset($result->errcode)) {
			throw new \Exception($result->errmsg, $result->errcode);
		}
		
		$this->info = $result;
	}
	
	/**
	 * 用户的access_token是否已经过期
	 * @return bool TRUE表示过期, FALSE表示未过期
	 * @throws \Exception
	 */
	public function getUserAccessTokenIsExpire() {
		if(!$this->info) {
			throw new \Exception('请先进行获取用户令牌操作');
		}
		
		$url = sprintf(API::IS_EXPIRE_USER_ACCESS_TOKEN, $this->info->access_token, $this->info->openid);
		$result = json_decode($this->get($url));
		if($result->errcode != 0) {
			throw new \Exception($result->errmsg, $result->errcode);
		}
		
		return $result->errmsg != 'ok';
	}
	
	/**
	 * 获取用户的具体信息（当scope为snsapi_userinfo的时候才可以获取）
	 * @param string $language 国家地区语言版本，zh_CN 简体，zh_TW 繁体，en 英语
	 * @return \stdClass 用户信息
	 * @throws \Exception
	 */
	public function getUserinfo($language = 'zh-CN') {
		if(!$this->info) {
			throw new \Exception('请先进行获取用户令牌操作');
		}
		
		if(empty($this->info->scope) || ($this->info->scope->scope != snsapi_userinfo)) {
			throw new \Exception('获取用户信息权限不足');
		}
		
		$url = sprintf(API::GET_ACCESS_TOKEN, $this->info->access_token, $this->info->openid, $language);
		$result = json_decode($this->get($url));
		if(isset($result->errcode)) {
			throw new \Exception($result->errmsg, $result->errcode);
		}
		
		return $result;
	}
}