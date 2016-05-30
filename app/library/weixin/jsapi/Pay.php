<?php

namespace weixin\jsapi;

class Pay extends Base {

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
	 * js支付封装
	 * @param \weixin\pay\UnifiedOrder $unifiedOrder 统一下单对象
	 * @param string $url 当前页面的url地址, 不包括#部分
	 * @return array 微信支付需要的json信息
	 */
	public function payment(\weixin\pay\UnifiedOrder $unifiedOrder, $url) {
		// 生成订单
		$unifiedOrder->setTradeType('JSAPI');
		$unifiedOrder->setDeviceInfo('WEB');
		$result = $unifiedOrder->payment();

		// 支付信息，由微信js进行调用
		$jsPays['timeStamp'] = time();
		$jsPays['appId'] = $this->getAppid();
		$jsPays['nonceStr'] = $this->strShuffle();
		$jsPays['signType'] = 'MD5';
		$jsPays['package'] = "prepay_id={$result['prepay_id']}";
		$jsPays['paySign'] = $this->sign($jsPays);

		// 封装支付信息
		$payInfo['pay'] = json_encode($jsPays);
		$payInfo['config'] = json_encode($this->getConfig($url));

		return $payInfo;
	}
}