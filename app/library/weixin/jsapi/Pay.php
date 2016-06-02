<?php

namespace weixin\jsapi;

class Pay extends Base {

	/**
	 * 统一下单对象
	 * @param \weixin\pay\UnifiedOrder
	 */
	private $unifiedOrder = NULL;
	
	/**
	 * 创建支付业务逻辑对象
	 * @param string $appSecret 公众号appSecret
	 * @param \weixin\pay\UnifiedOrder $unifiedOrder 统一下单对象
	 * @param \storage\Adapter $storage 存储对象
	 * @return void
	 */
	public function __construct($appSecret, \weixin\pay\UnifiedOrder $unifiedOrder, \storage\Adapter $storage) {
		$unifiedOrder->setTradeType('JSAPI');
		$unifiedOrder->setDeviceInfo('WEB');
		$this->unifiedOrder = $unifiedOrder;

		$this->setAppid($this->unifiedOrder->getAppid());
		$this->setAppSecret($appSecret);
		$this->setStorage($storage);
		$this->setAccessToken();
		$this->setJsApiTicket();
	}

	/**
	 * js支付
	 * @return array 微信支付需要的json信息
	 */
	public function payment() {
		$result = $this->unifiedOrder->payment();

		// 支付信息，由微信js进行调用
		$jsPays['timeStamp'] = time();
		$jsPays['appId'] = $this->getAppid();
		$jsPays['nonceStr'] = $this->strShuffle();
		$jsPays['signType'] = 'MD5';
		$jsPays['package'] = "prepay_id={$result->prepay_id}";
		$jsPays['paySign'] = $this->sign($jsPays);

		return $jsPays;
	}
}