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
	 * @param string $appid 公众号appid
	 * @param string $appSecret 公众号appSecret
	 * @param string $mchid 商户id
	 * @param string $key 商户密钥
	 * @param \storage\Adapter $storage 存储对象
	 * @return void
	 */
	public function __construct($appid, $appSecret, $mchid, $key, \storage\Adapter $storage) {
		$this->setAppid($appid);
		$this->setAppSecret($appSecret);
		$this->setMchid($mchid);
		$this->setKey(key);
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
	 * js支付
	 * @return array 微信支付需要的json信息
	 */
	public function payment() {
		// 生成订单
		if(!$this->unifiedOrder) {
			throw new \weixin\Exception('请设置统一下单对象', 1100);
		}
		$result = $this->unifiedOrder->payment();

		// 支付信息，由微信js进行调用
		$jsPays['timeStamp'] = time();
		$jsPays['appId'] = $this->appid;
		$jsPays['nonceStr'] = $this->strShuffle();
		$jsPays['signType'] = 'MD5';
		$jsPays['package'] = "prepay_id={$result['prepay_id']}";
		$jsPays['paySign'] = $this->sign($jsPays);

		return $jsPays;
	}
}