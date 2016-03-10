<?php

namespace Base;

/**
 * 微信基类
 */
abstract class WeixinController extends BaseController {

	/**
	 * 初始化检查
	 */
	public function init() {
		// 来源合法性检查
		if(!$this->checkSignature()) {
			header('HTTP/1.1 403 Forbidden');
			exit();
		}

		// 关闭模板
		$this->disView();
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
	 * 微信来源验证
	 * @return bool
	 */
	protected function checkSignature() {
		// 来源验证参数
		$request = $this->getRequest();
		$signature = $request->get('signature');
		$timestamp = $request->get('timestamp');
		$nonce = $request->get('nonce');

		// 检查来源合法性
		$sign = array(RESOURCE_TOKEN, $timestamp, $nonce);
		sort($sign, SORT_STRING);
		$sign = sha1(implode($sign));

		return $sign == $signature;
	}
}