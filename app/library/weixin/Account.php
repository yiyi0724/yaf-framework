<?php

/**
 * 获取用户的账号信息
 */
namespace Ku\Weixin;

class Account extends Base {

	/**
	 * 获取用户的基本信息
	 * @param string $openid 用户在公众号的openid
	 * @return array
	 */
	protected function getAccountInfo($openid) {
		// 请求的接口
		$api = sprintf('https://api.weixin.qq.com/cgi-bin/user/info?access_token=%s&openid=%s&lang=zh_CN ', $this->getAccessToken(), $openid);
		return json_decode($this->httpGet($api), TRUE);
	}
	
	/**
	 * 获取用户是否关注过公众号
	 * @param string $openid 用户在公众号的openid
	 * @return boolean
	 */
	public function isSubscribe($openid) {
		$result = $this->getAccountInfo($openid);
		return isset($result['subscribe']) && $result['subscribe'] == 1;
	}
}