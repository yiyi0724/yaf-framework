<?php

namespace Weixin\Wechat;

class Base {
	
	protected $token;
	
	protected $appid;
	
	protected $appsecret;
	
	protected $accessToken;
	
	/**
	 * 构造函数
	 * @param string $token token值
	 * @param string $appid appied值
	 * @param string $appsecret appsecret值
	 * @param \Network\Http $http http请求对象
	 */
	public function __construct($token, $appid, $appsecret, \Network\Http $http) {
		$this->token = $token;
		$this->appid = $appid;
		$this->appsecret = $appsecret;
		$this->http = $http;
		$this->accessToken = $this->getAccessToken();
	}
	
	protected function getAccessToken() {
		$accessToken = 
		$api = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$this->appid}&secret={$this->appsecret}";
		$this->http->setAction($api);
		$this->http->setDecode(\Network\Http::DECODE_JSON);
		$result = $this->http->get();
	}
	
	/**
	 * 微信来源验证
	 * @return bool
	 */
	public function checkSignature($signature, $timestamp, $nonce) {
		$sign = array($this->token, $timestamp, $nonce);
		sort($sign, SORT_STRING);
		$sign = sha1(implode($sign));	
		return $sign == $signature;
	}
	
	/**
	 * 获取用户授权信息
	 * @return array
	 */
	public function getOauthUser($code){
		$api = "https://api.weixin.qq.com/sns/oauth2/access_token?appid={$this->appid}&secret={$this->appsecret}&code={$code}&grant_type=authorization_code";
		$userOauthInfo = array(
			'access_token'  =>  $response->access_token,
			'open_id'       =>  $response->openid,
			'refresh_token' =>  $response->refresh_token,
			'expires_in'    =>  $response->expires_in,
			'scope'         =>  $response->scope,
			//'unionid'       =>  $response->unionid,   //unionid暂不需要
		);
		return $userOauthInfo ? $userOauthInfo : null;
	}
}