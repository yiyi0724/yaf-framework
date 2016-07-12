<?php

/**
 * jsapi操作类
 * @author enychen
 */
namespace weixin;

abstract class Jsapi extends Base {

	/**
	 * jsapi_ticket票据
	 * @var string
	 */
	private $jsapiTicket = NULL;

	/**
	 * jsapi_ticket要验证的接口
	 * @var array
	 */
	private $jsapiList = array();

	/**
	 * 生成jsapi_ticket需要的当前url地址，不包括#部分
	 * @var string
	 */
	private $url = NULL;

	/**
	 * 创建jsapi对象
	 * @param string $appid 公众号appid
	 * @param string $appSecret 公众号appSecret
	 * @param \storage\Adapter $storage 存储对象
	 */
	public function __construct($appid, $appSecret, \storage\Adapter $storage) {
		$this->setAppid($appid);
		$this->setAppSecret($appSecret);
		$this->setStorage($storage);
		$this->setAccessToken();
		$this->setJsApiTicket();
	}

	/**
	 * 设置jsapi_ticket票据
	 * @return Jsapi $this 返回当前对象进行连贯操作
	 */
	private function setJsApiTicket() {
		// 缓存appid的键
		$cacheKey = sprintf('weixin.jsapi_ticket.%s', $this->getAppid());

		// 之前获取的还没有到期
		$this->jsapiTicket = $this->getStorage()->get($cacheKey);
		if(!$this->jsapiTicket) {
			$api = 'https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=%s&type=jsapi';
			$result = json_decode($this->get(sprintf($api, $this->getAccessToken())), TRUE);
			if($result->errcode) {
				$this->throws(1101, $result->errmsg);
			}

			// 缓存jsapi_ticket
			$this->getStorage()->set($cacheKey, $result->ticket, $result->expires_in);

			// 设置变量
			$this->jsapiTicket = $result->ticket;
		}

		return $this;
	}

	/**
	 * 获取js票据
	 * @return string
	 */
	public function getJsapiTicket() {
		return $this->jsapiTicket;
	}

	/**
	 * 生成js_ticket需要的当前url地址，不包括#部分
	 * @var string $url url地址
	 * @return Jsapi $this 返回当前对象进行连贯操作
	 */
	public function setUrl($url) {
		$this->url = $url;
		return $this;
	}

	/**
	 * 获取url地址
	 * @return string
	 */
	public function getUrl() {
		return $this->url;
	}

	/**
	 * jsapi_ticket要验证的接口
	 * @param array $jsapiList 接口名称
	 * @return Jsapi $this 返回当前对象进行连贯操作
	 */
	public function setJsApiList(array $jsapiList) {
		$this->jsapiList = array_value($jsapiList);
		return $this;
	}

	/**
	 * 获取jsapi要验证的接口
	 * @return array
	 */
	public function getJsapiList() {
		return $this->jsapiList;
	}

	/**
	 * 获取wxConfig信息
	 * @return string js接口验证的信息
	 */
	public function getWxConfig() {
		if(!$this->getUrl()) {
			$this->throws(1102, '请先设置url地址');
		}

		$wxConfig['appId'] = $this->getAppid();
		$wxConfig['timestamp'] = time();
		$wxConfig['nonceStr'] = $this->strShuffle();
		$wxConfig['jsapi_ticket'] = $this->getJsapiTicket();
		$signature = "jsapi_ticket={$wxConfig['jsapi_ticket']}&noncestr={$wxConfig['nonceStr']}";
		$signature .= "&timestamp={$wxConfig['timestamp']}&url={$this->getUrl()}";
		$wxConfig['signature'] = sha1($signature);
		$wxConfig['jsApiList'] = json_encode($this->getJsapiList());

		return json_encode($wxConfig);
	}
}