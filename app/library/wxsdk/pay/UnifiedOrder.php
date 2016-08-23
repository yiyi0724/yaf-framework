<?php

/**
 * 统一下单
 * @author enychen
 */
namespace wxsdk\pay;

class UnifiedOrder extends Base {

	/**
	 * 统一下单接口
	 * @var string
	 */
	const UNIFIED_ORDER_API = 'https://api.mch.weixin.qq.com/pay/unifiedorder';

	/**
	 * 设置订单号，必须
	 * @param string $outTradeNo 订单号
	 * @return UnifiedOrder $this 返回当前对象进行连贯操作
	 */
	public function setOutTradeNo($outTradeNo) {
		$this->info['out_trade_no'] = $outTradeNo;
		return $this;
	}

	/**
	 * 获取订单号
	 * @return string
	 */
	public function getOutTradeNo() {
		return $this->getInfo('out_trade_no');
	}

	/**
	 * 设置价格（内部会把价格转成分），必须
	 * @param number $totalFee 价格，单位：元
	 * @return UnifiedOrder $this 返回当前对象进行连贯操作
	 */
	public function setTotalFee($totalFee) {
		$this->info['total_fee'] = $totalFee * 100;
		return $this;
	}

	/**
	 * 获取价格
	 * @return number
	 */
	public function getTotalFee() {
		$totalFee = $this->getInfo('total_fee', 0);
		return $totalFee/100;
	}

	/**
	 * 设置商品或支付单简要描述，必须
	 * @param string $body 商品或支付单简要描述
	 * @return UnifiedOrder $this 返回当前对象进行连贯操作
	 */
	public function setBody($body) {
		$this->info['body'] = $body;
		return $this;
	}

	/**
	 * 获取商品或支付单简要描述
	 * @return string
	 */
	public function getBody() {
		return $this->getInfo('body');
	}

	/**
	 * 设置支付方式，必须
	 * @param string $tradeType JSAPI，NATIVE，APP，WAP四种支付情况
	 * @return UnifiedOrder $this 返回当前对象进行连贯操作
	 */
	public function setTradeType($tradeType) {
		$this->info['trade_type'] = strtoupper($tradeType);
		return $this;
	}

	/**
	 * 获取支付方式
	 * @return string
	 */
	public function getTradeType() {
		return $this->getInfo('trade_type');
	}

	/**
	 * 设置APP和网页支付提交用户端ip，Native支付填调用微信支付API的机器IP, 必须
	 * @param string $ip ip地址
	 * @return UnifiedOrder $this 返回当前对象进行连贯操作
	 */
	public function setSpbillCreateIp($ip) {
		$this->info['spbill_create_ip'] = $ip;
		return $this;
	}

	/**
	 * 获取APP和网页支付提交用户端ip
	 * @return string
	 */
	public function getSpbillCreateIp() {
		return $this->getInfo('spbill_create_ip');
	}

	/**
	 * 设置回调地址，按需
	 * @param string $notifyUrl, 操作成功后微信回调我司的URL地址
	 * @return UnifiedOrder $this 返回当前对象进行连贯操作
	 */
	public function setNotifyUrl($notifyUrl) {
		$this->info['notify_url'] = $notifyUrl;
		return $this;
	}

	/**
	 * 获取回调地址
	 * @return string
	 */
	public function getNotifyUrl() {
		return $this->getInfo('notify_url');
	}

	/**
	 * 设置用户在商户appid下的唯一标识，trade_type=JSAPI的时候此参数必传，按需
	 * @param string $openId 用户的openid
	 * @return UnifiedOrder $this 返回当前对象进行连贯操作
	 */
	public function setOpenid($openId) {
		$this->info['openid'] = $openId;
		return $this;
	}

	/**
	 * 获取用户在商户appid下的唯一标识
	 * @return string
	 */
	public function getOpenid() {
		return $this->getInfo('openid');
	}

	/**
	 * 设置二维码中包含的商品ID，商户自行定义，trade_type=NATIVE的时候此参数必传，按需
	 * @param number|string $productId 商品id
	 * @return UnifiedOrder $this 返回当前对象进行连贯操作
	 */
	public function setProductId($productId) {
		$this->info['product_id'] = $productId;
		return $this;
	}

	/**
	 * 获取二维码中包含的商品ID
	 * @return number|string
	 */
	public function getProductId() {
		return $this->getInfo('product_id');
	}

	/**
	 * 设置货币种类，默认人民币(CNY)，可选
	 * @param string $feeType 货币种类
	 * @return UnifiedOrder $this 返回当前对象进行连贯操作
	 */
	public function setFeeType($feeType) {
		$this->info['fee_type'] = $feeType;
		return $this;
	}

	/**
	 * 获取货币种类
	 * @return string
	 */
	public function getFeeType() {
		return $this->getInfo('fee_type');
	}

	/**
	 * 设置终端设备号(门店号或收银设备ID)，注意：PC网页或公众号内支付请传"WEB", 可选
	 * @param string $deviceInfo 设备号
	 * @return UnifiedOrder $this 返回当前对象进行连贯操作
	 */
	public function setDeviceInfo($deviceInfo) {
		$this->info['device_info'] = $deviceInfo;
		return $this;
	}

	/**
	 * 获取终端设备号(门店号或收银设备ID)
	 * @return string
	 */
	public function getDeviceInfo() {
		return $this->getInfo('device_info');
	}

	/**
	 * 设置不使用信用卡支付, 可选
	 * @return UnifiedOrder $this 返回当前对象进行连贯操作
	 */
	public function setNoCredit() {
		$this->info['limit_pay'] = 'no_credit';
		return $this;
	}

	/**
	 * 获取不使用信用卡支付
	 * @return string|null
	 */
	public function getNoCredit() {
		return $this->getInfo('limit_pay');
	}

	/**
	 * 设置商品标记，代金券或立减优惠功能的参数, 可选
	 * @param number|string $goodsTag 商品标记
	 * @return UnifiedOrder $this 返回当前对象进行连贯操作
	 */
	public function setGoodsTag($goodsTag) {
		$this->info['goods_tag'] = $goodsTag;
		return $this;
	}

	/**
	 * 获取商品标记，代金券或立减优惠功能的参数
	 * @return number|string
	 */
	public function getGoodsTag() {
		return $this->getInfo('goods_tag');
	}

	/**
	 * 设置交易生成时间,格式为yyyyMMddHHmmss, 可选
	 * @param string $timeStart 交易生成时间
	 * @return UnifiedOrder $this 返回当前对象进行连贯操作
	 */
	public function setTimeStart($timeStart) {
		$this->info['time_start'] = $timeStart;
		return $this;
	}

	/**
	 * 获取交易生成时间
	 * @return string
	 */
	public function getTimeStart() {
		return $this->getInfo('time_start');
	}

	/**
	 * 设置交易截止时间, 获取订单失效时间，格式为yyyyMMddHHmmss, 最短失效时间间隔必须大于5分钟, 可选
	 * @param string $timeExpire 交易截止时间
	 * @return UnifiedOrder $this 返回当前对象进行连贯操作
	 */
	public function setTimeExpire($timeExpire) {
		$this->info['time_expire'] = $timeExpire;
		return $this;
	}

	/**
	 * 获取交易截止时间
	 * @return string
	 */
	public function getTimeExpire() {
		return $this->getInfo('time_expire');
	}

	/**
	 * 设置商品名称明细列表, 可选
	 * @param string $detail 交易截止时间
	 * @return UnifiedOrder $this 返回当前对象进行连贯操作
	 */
	public function setDetail($detail) {
		$this->info['detail'] = $detail;
		return $this;
	}

	/**
	 * 获取商品名称明细列表
	 * @return string
	 */
	public function getDetail() {
		return $this->getInfo('detail');
	}

	/**
	 * 设置附加数据，在查询API和支付通知中原样返回，该字段主要用于商户携带订单的自定义数据, 可选
	 * @param string $attach 交易截止时间
	 * @return UnifiedOrder $this 返回当前对象进行连贯操作
	 */
	public function setAttach($attach) {
		$this->info['attach'] = $attach;
		return $this;
	}

	/**
	 * 获取附加数据
	 * @return string
	 */
	public function getAttach() {
		return $this->getInfo('attach');
	}

	/**
	 * jsapi支付
	 * @return string 支付封装的json字符串
	 * @throws \wxsdk\WxException
	 */
	public function jsapi() {
		// 必须的业务参数检查
		if(!$this->getOpenid()) {
			$this->throws(1000110, '请设置openid');
		}

		// 设置参数信息
		$this->setTradeType('JSAPI');
		$this->setDeviceInfo('WEB');

		// 执行公共调用
		$result = $this->executeHaveNotify($unifiedOrderObject);

		// 支付信息
		$jsapi['appId'] = $this->getAppid();
		$jsapi['nonceStr'] = $this->strShuffle();
		$jsapi['signType'] = 'MD5';
		$jsapi['package'] = "prepay_id={$result['prepay_id']}";
		$jsapi['paySign'] = $this->sign($jsPays);

		return json_encode($jsapi);
	}

	/**
	 * 扫码支付
	 * @return string 二维码参数值
	 * @throws \wxsdk\WxException
	 */
	public function native() {
		// 必须的业务参数检查
		if($this->getProductId()) {
			$this->throws(1000111, '请设置product_id');
		}
		
		// 设置参数信息
		$this->setTradeType('NATIVE');

		// 执行公共调用
		$result = $this->executeHaveNotify($unifiedOrderObject);

		return $result['code_url'];
	}

	/**
	 * app支付
	 * @return array 针对app调用封装好的参数列表
	 * @throw \wxsdk\WxException
	 */
	public function app() {
		// 设置参数信息
		$this->setTradeType('APP');

		// 执行公共调用
		$result = $this->executeHaveNotify($unifiedOrderObject);

		// 封装返回数据
		$app['appid'] = $this->getAppid();
		$app['partnerid'] = $this->getMchid();
		$app['prepayid'] = $result['prepay_id'];
		$app['noncestr'] = $this->strShuffle();
		$app['package'] = 'Sign=WXPay';
		$app['timestamp'] = time();
		$app['sign'] = $this->sign($result);
		return $app;
	}

	/**
	 * 统一下单，使用回调地址
	 * @return array https://pay.weixin.qq.com/wiki/doc/api/native.php?chapter=9_1
	 * @throws \wxsdk\WxException
	 */
	protected function executeHaveNotify() {
		// 必传参数检查
		if(!$this->getOutTradeNo()) {
			$this->throws(100015, '请设置订单号');
		}
		if(!$this->getTotalFee()) {
			$this->throws(100016, '请设置价格');
		}
		if(!$this->getBody()) {
			$this->throws(100017, '请设置商品描述信息');
		}
		if(!in_array($this->getTradeType(), array('JSAPI', 'NATIVE', 'APP', 'WAP'))) {
			$this->throws(100018, "请设置交易类型");
		}
		if(!$this->getNotifyUrl()) {
			$this->throws(100019, "请设通知地址");
		}

		// 支付参数整合
		$order = $this->toArray();
		$order['appid'] = $this->getAppid();
		$order['mch_id'] = $this->getMchid();
		$order['nonce_str'] = $this->strShuffle();
		$order['sign'] = $this->sign($order);

		// xml编码
		$order = $this->xmlEncode($order);

		// curl微信生成订单
		$result = $this->post(self::UNIFIED_ORDER_API, $order);
		
		// 回传参数检查
		$this->checkSignature($result);

		return $this->xmlDecode($result);
	}
}