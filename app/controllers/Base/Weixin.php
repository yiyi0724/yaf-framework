<?php

namespace Base;

/**
 * 微信基类
 */
abstract class WeixinController extends BaseController {
	
	protected $wechat;

	/**
	 * 初始化检查
	 */
	public function init() {		
		$this->initWechat();
	}

	/**
	 * 获取微信来的参数信息
	 * @return array
	 */
	public function getParams() {
		$params = file_get_contents('php://input');
		$params = simplexml_load_string($params, 'SimpleXMLElement', LIBXML_NOCDATA);
		return json_decode(json_encode($params), TRUE);
	}

	/**
	 * 初始化微信
	 * @return bool
	 */
	protected function initWechat() {		
		$http = new \Network\Http();
		$this->wechat = new \Weixin\Wechat\Base(RESOURCE_TOKEN, RESOURCE_APPID, RESOURCE_APPSECRET, $http);
		
		// 来源验证参数
		$request = $this->getRequest();
		$signature = $request->get('signature');
		$timestamp = $request->get('timestamp');
		$nonce = $request->get('nonce');
		if(!$this->wechat->checkSignature($signature, $timestamp, $nonce)) {
			header('HTTP/1.1 403 Forbidden');
			exit();
		}
	}
}