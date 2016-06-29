<?php

namespace weixin\jsapi;

abstract class Base extends \weixin\Base {

	/**
	 * 缓存ticket票据
	 * @var string
	 */
	const JSAPI_TICKET = 'weixin.jsapi.ticket';

	/**
	 * jsapi_ticket票据
	 * @var string
	 */
	protected $jsApiTicket = NULL;

	/**
	 * 生成jsapi_ticket需要的当前url地址，不包括#部分
	 * @var string
	 */
	protected $url;

	/**
	 * jsapi_ticket要验证的接口
	 * @var array
	 */
	protected $jsApiList = array();
	
	/**
	 * 设置jsapi_ticket票据
	 * @return boolean
	 */
	protected function setJsApiTicket(){	
		// 之前获取的还没有到期
		if($this->jsApiTicket = $this->storage->get(self::JSAPI_TICKET)) {
			return TRUE;
		}

		// 走微信接口进行请求
		$url = sprintf(\weixin\API::GET_JSAPI_TICKET, $this->accessToken);
		$result = json_decode($this->get($url));
		if($result->errcode != 0) {
			$this->throws($result->errcode, $result->errmsg);
		}
	
		// 缓存access_token
		$this->storage->set(self::JSAPI_TICKET, $result->ticket);
		$this->storage->expire(self::JSAPI_TICKET, $result->expires_in);
	
		// 设置变量
		$this->jsApiTicket = $result->ticket;
	
		return TRUE;
	}

	/**
	 * 获取js票据
	 * @return string
	 */
	public function getJsApiTicket() {
		return $this->jsApiTicket;
	}

	/**
	 * 生成js_ticket需要的当前url地址，不包括#部分
	 * @var string $url url地址
	 * @return void
	 */
	public function setUrl($url) {
		$this->url = $url;
	}

	/**
	 * jsapi_ticket要验证的接口
	 * @param array $jsApiList 接口名称
	 * @return void
	 */
	public function setJsApiList(array $jsApiList) {
		$this->jsApiList = array_value($jsApiList);
	}	

	/**
	 * 获取wxConfig信息
	 * @return string
	 */
	public function getWxConfig() {
		if(!$this->url) {
			$this->throws(1101, '请先设置url地址');
		}

		$wxConfig['appId'] = $this->appid;
		$wxConfig['timestamp'] = time();
		$wxConfig['nonceStr'] = $this->strShuffle();
		$wxConfig['jsapi_ticket'] = $this->getJsTicket();
		$signature = "jsapi_ticket={$wxConfig['jsapi_ticket']}&noncestr={$wxConfig['noncestr']}";
		$signature .= "&timestamp={$wxConfig['timestamp']}&url={$this->url}";
		$wxConfig['signature']  = sha1($signature);
		$wxConfig['jsApiList'] = json_encode($this->jsApiList);

		return json_encode($wxConfig);
	}
}