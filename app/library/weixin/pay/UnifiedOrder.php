<?php

/**
 * 统一下单
 * @author enychen
 */
namespace weixin\pay;

class UnifiedOrder extends Base {

	/**
	 * 订单信息
	 * @var array
	 */
	private $order = array();

	/**
	 * 预支付信息
	 * @var array
	 */
	private $result = array();

	/**
	 * 设置订单号，必须
	 * @param string $outTradeNo 订单号
	 * @return void
	 */
	public function setOutTradeNo($outTradeNo) {
		$this->order['out_trade_no'] = $outTradeNo;
	}

	/**
	 * 获取订单号
	 * @return string
	 */
	public function getOutTradeNo() {
		return $this->order['out_trade_no'];
	}

	/**
	 * 设置价格，必须
	 * @param number $totalFee 价格，单位：元（内部会把价格转成分）
	 * @return void
	 */
	public function setTotalFee($totalFee) {
		$this->order['total_fee'] = $totalFee * 100;
	}

	/**
	 * 获取价格
	 * @return string
	 */
	public function getTotalFee() {
		return $this->order['total_fee'];
	}

	/**
	 * 设置商品或支付单简要描述，必须
	 * @param string $body 商品或支付单简要描述
	 * @return void
	 */
	public function setBody($body) {
		$this->order['body'] = $body;
	}

	/**
	 * 获取商品或支付单简要描述
	 * @return string
	 */
	public function getBody() {
		return $this->order['body'];
	}

	/**
	 * 设置支付方式，必须
	 * @param string $tradeType JSAPI，NATIVE，APP，WAP
	 * @return void
	 */
	public function setTradeType($tradeType) {
		$this->order['trade_type'] = strtoupper($tradeType);
	}

	/**
	 * 获取支付方式
	 * @return string
	 */
	public function getBody() {
		return $this->order['trade_type'];
	}

	/**
	 * 设置APP和网页支付提交用户端ip，Native支付填调用微信支付API的机器IP, 必须
	 * @param string $ip ip地址
	 * @return void
	 */
	public function setSpbillCreateIp($ip) {
		$this->order['spbill_create_ip'] = $ip;
	}

	/**
	 * 获取APP和网页支付提交用户端ip
	 * @return string
	 */
	public function getSpbillCreateIp() {
		return $this->order['spbill_create_ip'];
	}

	/**
	 * 模式二支付必须，模式一支付可选，按需
	 * @param string $notifyUrl, 操作成功后微信回调我司的URL地址
	 * @return void
	 */
	public function setNotifyUrl($notifyUrl) {
		$this->order['notify_url'] = $notifyUrl;
	}

	/**
	 * 获取回调参数
	 * @return multitype:
	 */
	public function getNotifyUrl() {
		return $this->order['notify_url'];
	}

	/**
	 * 设置用户在商户appid下的唯一标识，trade_type=JSAPI的时候此参数必传，按需
	 * @param string $openId 用户的openid
	 * @return void
	 */
	public function setOpenid($openId) {
		$this->order['openid'] = $openId;
	}

	/**
	 * 获取用户在商户appid下的唯一标识
	 * @return string
	 */
	public function getOpenid() {
		return $this->order['openid'];
	}

	/**
	 * 设置二维码中包含的商品ID，商户自行定义，trade_type=NATIVE的时候此参数必传。，按需
	 * @param number|string $productId 商品id
	 * @return void
	 */
	public function setProductId($productId) {
		$this->order['product_id'] = $productId;
	}

	/**
	 * 获取二维码中包含的商品ID
	 * @return number|string
	 */
	public function getProductId() {
		return $this->order['product_id'];
	}

	/**
	 * 设置货币种类，默认人民币(CNY)，可选
	 * @param string $feeType 货币种类
	 * @return void
	 */
	public function setFeeType($feeType) {
		$this->order['fee_type'] = $feeType;
	}

	/**
	 * 获取货币种类
	 * @return string
	 */
	public function getFeeType() {
		return $this->order['fee_type'];
	}

	/**
	 * 设置终端设备号(门店号或收银设备ID)，注意：PC网页或公众号内支付请传"WEB", 可选
	 * @param string $deviceInfo 设备号
	 * @return void
	 */
	public function setDeviceInfo($deviceInfo) {
		$this->order['device_info'] = $deviceInfo;
	}

	/**
	 * 获取终端设备号(门店号或收银设备ID)
	 * @return string
	 */
	public function getDeviceInfo() {
		return $this->order['device_info'];
	}

	/**
	 * 设置不使用信用卡支付, 可选
	 * @param string $limitPay 只有一个值，no_credit
	 * @return void
	 */
	public function setLimitPay($limitPay = 'no_credit') {
		$this->order['limit_pay'] = $limitPay;
	}

	/**
	 * 获取不使用信用卡支付
	 * @return string|null
	 */
	public function getLimitPay() {
		return $this->order['limit_pay'];
	}

	/**
	 * 设置商品标记，代金券或立减优惠功能的参数, 可选
	 * @param number|string $goodsTag 商品标记
	 * @return void
	 */
	public function setGoodsTag($goodsTag) {
		$this->order['goods_tag'] = $goodsTag;
	}

	/**
	 * 获取商品标记，代金券或立减优惠功能的参数
	 * @return number|string
	 */
	public function getGoodsTag() {
		return $this->order['goods_tag'];
	}

	/**
	 * 设置交易生成时间,格式为yyyyMMddHHmmss, 可选
	 * @param string $timeStart 交易生成时间
	 * @return void
	 */
	public function setTimeStart($timeStart) {
		$this->order['time_start'] = $timeStart;
	}

	/**
	 * 获取交易生成时间
	 * @return string
	 */
	public function getTimeStart() {
		return $this->order['time_start'];
	}	

	/**
	 * 设置交易截止时间, 获取订单失效时间，格式为yyyyMMddHHmmss, 最短失效时间间隔必须大于5分钟, 可选
	 * @param string $timeExpire 交易截止时间
	 * @return void
	 */
	public function setTimeExpire($timeExpire) {
		$this->order['time_expire'] = $timeExpire;
	}

	/**
	 * 获取交易截止时间
	 * @return string
	 */
	public function getTimeExpire() {
		return $this->order['time_expire'];
	}

	/**
	 * 设置商品名称明细列表, 可选
	 * @param string $timeExpire 交易截止时间
	 * @return void
	 */
	public function setDetail($detail) {
		$this->order['detail'] = $detail;
	}

	/**
	 * 获取商品名称明细列表
	 * @return string
	 */
	public function getDetail() {
		return $this->order['detail'];
	}

	/**
	 * 设置附加数据，在查询API和支付通知中原样返回，该字段主要用于商户携带订单的自定义数据, 可选
	 * @param string $timeExpire 交易截止时间
	 * @return void
	 */
	public function setAttach($attach) {
		$this->order['attach'] = $attach;
	}

	/**
	 * 获取附加数据
	 * @return string
	 */
	public function getAttach() {
		return $this->order['attach'];
	}

	/**
	 * 设置预支付交易会话标识，返回结果进行的设置
	 * @param string $prepayId 预支付交易会话标识
	 * @return void
	 */
	protected function setPrepayId($prepayId) {
		$this->result['prepay_id'] = $prepayId;
	}

	/**
	 * 获取预支付交易会话标识
	 * @return string
	 */
	public function getPrepayId() {
		return $this->result['prepay_id'];
	}

	/**
	 * 设置二维码支付链接
	 * @param string $codeUrl 二维码支付链接
	 * @return void
	 */
	public function setCodeUrl($codeUrl) {
		$this->result['code_url'] = $codeUrl;
	}

	/**
	 * 获取二维码支付链接
	 * @return string
	 */
	public function getCodeUrl() {
		return $this->result['code_url'];
	}

	/**
	 * 获取订单全部信息
	 * @return array
	 */
	public function getOrder() {
		return $this->order;
	}

	/**
	 * 获取预支付结果信息
	 * @return array
	 */
	public function getResult() {
		return $this->result;
	}

	/**
	 * 统一下单
	 * 文档地址：https://pay.weixin.qq.com/wiki/doc/api/native.php?chapter=4_2
	 * @return void
	 */
	public function payment() {
		// 必传参数检查
		if(empty($this->order['out_trade_no'])) {
			// 订单号检查
			$this->throws(1000, '请设置订单号');
		} else if(empty($this->order['total_fee'])) {
			// 价格检查
			$this->throws(1001, '请设置价格');
		} else if(empty($this->order['body'])) {
			// 商品描述信息检查
			$this->throws(1002, '请设置商品描述信息');
		} else if(empty($this->order['trade_type']) || !in_array($this->order['trade_type'], array('JSAPI', 'NATIVE', 'APP', 'WAP'))) {
			// 交易类型检查
			$this->throws(1003, "微信不支持{$this->order['trade_type']}交易类型");
		}

		// 业务参数检查
		if($this->order['trade_type'] == 'JSAPI' && empty($this->order['openid'])) {
			// JSAPI交易类型openid检查
			$this->throws(1004, '请设置openid');
		} else if($this->order['trade_type'] == 'NAVITE' && empty($this->order['product_id'])) {
			// NAVITE交易类型product_id检查
			$this->throws(1005, '请设置product_id');
		}

		// 拼接公共参数
		if(empty($this->order['spbill_create_ip'])) {
			// 设置交易ip地址
			$this->order['spbill_create_ip'] = $_SERVER['REMOTE_ADDR'];
		}
		$this->order['appid'] = $this->getAppid();
		$this->order['mch_id'] = $this->getMchid();
		$this->order['nonce_str'] = $this->strShuffle();
		$this->order['sign'] = $this->sign($this->order);
		
		// xml编码
		$params = $this->XmlEncode($this->order);
		$this->order = array();
		
		// curl微信生成订单
		$result = $this->post(\weixin\API::PAY_UNIFIED_ORDER, $params);
		$result = $this->verify($result);

		$this->setPrepayId($result->prepay_id);
		$this->setCodeUrl($result->code_url);
	}
}