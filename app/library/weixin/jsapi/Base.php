<?php

namespace weixin\jsapi;

abstract class Base extends \weixin\Base {

	/**
	 * 缓存ticket票据
	 * @var string
	 */
	const JSAPI_TICKET = 'weixin.jsapi.ticket';

	/**
	 * js_api_ticket票据
	 * @var string
	 */
	protected $jsApiTicket = NULL;
	
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
			throw new \weixin\Exception($result->errmsg, $result->errcode);
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
	 * 获取jsconfig信息
	 * @param string $url 当前页面的url地址, 不包括#部分
	 * @return array
	 */
	public function getConfig($url) {
		$jsConfig['timestamp'] = time();
		$jsConfig['noncestr'] = $this->strShuffle();
		$jsConfig['jsapi_ticket'] = $this->getJsTicket();
		$jsConfig['url'] = $url;
		$jsConfig['sign'] = sha1("jsapi_ticket={$jsConfig['jsapi_ticket']}&noncestr={$jsConfig['noncestr']}&timestamp={$jsConfig['timestamp']}&url={$jsConfig['url']}");

		return $jsConfig;
	}
}