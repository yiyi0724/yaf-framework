<?php

namespace weixin\jsapi;

class Pay extends Base {

	/**
	 * 统一下单对象
	 * @param \weixin\pay\UnifiedOrder
	 */
	private $unifiedOrder = NULL;

	/**
	 * 预支付信息
	 * @var array
	 */
	private $preparePay = array();
	
	/**
	 * 创建支付业务逻辑对象
	 * @param string $appSecret 公众号appSecret
	 * @param \weixin\pay\UnifiedOrder $unifiedOrder 统一下单对象
	 * @param \storage\Adapter $storage 存储对象
	 * @return void
	 */
	public function __construct($appSecret, \weixin\pay\UnifiedOrder $unifiedOrder, \storage\Adapter $storage) {
		$this->setUnifiedOrder($unifiedOrder);
		$this->setAppid($this->unifiedOrder->getAppid());
		$this->setAppSecret($appSecret);
		$this->setStorage($storage);
		$this->setAccessToken();
		$this->setJsApiTicket();
	}

	/**
	 * 设置统一下单对象
	 * @param \weixin\pay\UnifiedOrder $unifiedOrder 统一下单对象
	 * @return void
	 */
	public function setUnifiedOrder(\weixin\pay\UnifiedOrder $unifiedOrder) {
		$unifiedOrder->setTradeType('JSAPI');
		$unifiedOrder->setDeviceInfo('WEB');
		$this->unifiedOrder = $unifiedOrder;
	}

	/**
	 * 获取统一下单对象
	 * @return \weixin\pay\UnifiedOrder
	 */
	public function getUnifiedOrder() {
		return $unifiedOrder;
	}

	/**
	 * 设置验证时间戳
	 * @param number $time 时间戳
	 * @return void
	 */
	public function setTimeStamp($time) {
		$this->preparePay['timeStamp'] = $time;
	}

	/**
	 * 获取验证验证时间戳
	 * @return number
	 */
	public function getTimeStamp() {
		return $this->preparePay['timeStamp'];
	}

	public function getPreparyPay() {
		
	}

}