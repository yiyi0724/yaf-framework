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
	 * 设置订单号，必须
	 * @param string $outTradeNo 订单号
	 * @return void
	 */
	public function setOutTradeNo($outTradeNo) {
		$this->order['out_trade_no'] = $outTradeNo;
	}

	/**
	 * 设置价格，必须
	 * @param int $totalFee 价格，单位：元（内部会把价格转成分）
	 * @return void
	 */
	public function setTotalFee($totalFee) {
		$this->order['total_fee'] = $totalFee * 100;
	}

	/**
	 * 商品或支付单简要描述，必须
	 * @param string $body 商品或支付单简要描述
	 * @return void
	 */
	public function setBody($body) {
		$this->order['body'] = $body;
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
	 * APP和网页支付提交用户端ip，Native支付填调用微信支付API的机器IP, 必须
	 * @param string $ip ip地址
	 * @return void
	 */
	public function setSpbillCreateIp($ip) {
		$this->order['spbill_create_ip'] = $ip;
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
	 * trade_type=JSAPI，此参数必传，用户在商户appid下的唯一标识，按需
	 * @param string $openId 用户的openid
	 * @return void
	 */
	public function setOpenid($openId) {
		$this->order['openid'] = $openId;
	}

	/**
	 * trade_type=NATIVE，此参数必传。此id为二维码中包含的商品ID，商户自行定义，按需
	 * @param int|string $productId 商品id
	 * @return void
	 */
	public function setProductId($productId) {
		$this->order['product_id'] = $productId;
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
	 * 终端设备号(门店号或收银设备ID)，注意：PC网页或公众号内支付请传"WEB", 可选
	 * @param string $deviceInfo 设备号
	 * @return void
	 */
	public function setDeviceInfo($deviceInfo) {
		$this->order['device_info'] = $deviceInfo;
	}

	/**
	 * 不使用信用卡支付, 可选
	 * @return void
	 */
	public function setLimitPay($limitPay = 'no_credit') {
		$this->order['limit_pay'] = $limitPay;
	}

	/**
	 * 商品标记，代金券或立减优惠功能的参数, 可选
	 * @param int|string $goodsTag 商品标记
	 * @return void
	 */
	public function setGoodsTag($goodsTag) {
		$this->order['goods_tag'] = $goodsTag;
	}

	/**
	 * 交易生成时间,格式为yyyyMMddHHmmss, 可选
	 * @param string $timeStart 交易生成时间
	 * @return void
	 */
	public function setTimeStart($timeStart) {
		$this->order['time_start'] = $timeStart;
	}

	/**
	 * 交易截止时间, 获取订单失效时间，格式为yyyyMMddHHmmss, 最短失效时间间隔必须大于5分钟, 可选
	 * @param string $timeExpire 交易截止时间
	 * @return void
	 */
	public function setTimeExpire($timeExpire) {
		$this->order['time_expire'] = $timeExpire;
	}

	/**
	 * 商品名称明细列表, 可选
	 * @param string $timeExpire 交易截止时间
	 * @return void
	 */
	public function setDetail($detail) {
		$this->order['detail'] = $detail;
	}

	/**
	 * 附加数据，在查询API和支付通知中原样返回，该字段主要用于商户携带订单的自定义数据, 可选
	 * @param string $timeExpire 交易截止时间
	 * @return void
	 */
	public function setAttach($attach) {
		$this->order['attach'] = $attach;
	}

	/**
	 * 统一下单
	 * 文档地址：https://pay.weixin.qq.com/wiki/doc/api/native.php?chapter=4_2
	 * @param array $params 参数请参考文档
	 * @return array 返回获得的数组
	 */
	public function payment() {
		// 订单号检查
		if(!$this->order['out_trade_no']) {
			$this->throws(1000, '请设置订单号');
		}
		// 价格检查
		if(!$this->order['total_fee']) {
			$this->throws(1001, '请设置价格');
		}
		// 商品描述信息检查
		if(!$this->order['body']) {
			$this->throws(1002, '请设置商品描述信息');
		}
		// 交易类型检查
		if(!in_array($this->order['trade_type'], array('JSAPI', 'NATIVE', 'APP', 'WAP'))) {
			$this->throws(1003, "微信不支持{$this->order['trade_type']}交易类型");
		}
		// 设置交易ip地址
		if(!$this->order['spbill_create_ip']) {
			$this->order['spbill_create_ip'] = $_SERVER['REMOTE_ADDR'];
		}
		// JSAPI交易类型openid检查
		if($this->order['trade_type'] == 'JSAPI' && !$this->order['openid']) {
			$this->throws(1004, '请设置openid');
		}
		// NAVITE交易类型product_id检查
		if($this->order['trade_type'] == 'NAVITE' && !$this->order['product_id']) {
			$this->throws(1005, '请设置product_id');
		}
		
		// 拼接公共参数
		$this->order['appid'] = $this->appid;
		$this->order['mch_id'] = $this->mchid;
		$this->order['nonce_str'] = $this->strShuffle();
		$this->order['sign'] = $this->sign($this->order);
		
		// xml编码
		$params = $this->XmlEncode($this->order);
		$this->order = array();
		
		// curl微信生成订单
		$result = $this->post(\weixin\API::PAY_UNIFIED_ORDER, $params);
		$result = $this->verify($result);

		return $result;
	}
}