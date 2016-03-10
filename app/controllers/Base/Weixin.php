<?php

namespace Base;

/**
 * 微信基类
 */
abstract class WeixinController extends BaseController {

	/**
	 * 获取微信来的参数信息
	 * @return array
	 */
	public function getSource() {
		$params = file_get_contents('php://input');
		$params = simplexml_load_string($params, 'SimpleXMLElement', LIBXML_NOCDATA);
		return json_decode(json_encode($params), TRUE);
	}
}