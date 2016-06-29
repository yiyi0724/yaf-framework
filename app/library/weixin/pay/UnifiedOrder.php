<?php

/**
 * 统一下单
 * @author enychen
 */
namespace weixin\pay;

class UnifiedOrder {

	/**
	 * 订单信息
	 * @var array
	 */
	private $order = array();

	/**
	 * 构造函数
	 * @return void
	 */
	public function __construct() {
		// 设置默认ip地址
		$this->setSpbillCreateIp($_SERVER['REMOTE_ADDR']);
	}

	/**
	 * 设置订单号，必须
	 * @param string $outTradeNo 订单号
	 * @return UnifiedOrder $this 返回当前对象进行连贯操作
	 */
	public function setOutTradeNo($outTradeNo) {
		$this->order['out_trade_no'] = $outTradeNo;
		return $this;
	}

	/**
	 * 获取订单号
	 * @return string
	 */
	public function getOutTradeNo() {
		return $this->get('out_trade_no');
	}

	/**
	 * 设置价格，必须
	 * @param number $totalFee 价格，单位：元（内部会把价格转成分）
	 * @return UnifiedOrder $this 返回当前对象进行连贯操作
	 */
	public function setTotalFee($totalFee) {
		$this->order['total_fee'] = $totalFee * 100;
		return $this;
	}

	/**
	 * 获取价格
	 * @return number
	 */
	public function getTotalFee() {
		$totalFee = $this->get('total_fee', 0);
		return $totalFee/100;
	}

	/**
	 * 设置商品或支付单简要描述，必须
	 * @param string $body 商品或支付单简要描述
	 * @return UnifiedOrder $this 返回当前对象进行连贯操作
	 */
	public function setBody($body) {
		$this->order['body'] = $body;
		return $this;
	}

	/**
	 * 获取商品或支付单简要描述
	 * @return string
	 */
	public function getBody() {
		return $this->get('body');
	}

	/**
	 * 设置支付方式，必须
	 * @param string $tradeType JSAPI，NATIVE，APP，WAP四种支付情况
	 * @return UnifiedOrder $this 返回当前对象进行连贯操作
	 */
	public function setTradeType($tradeType) {
		$this->order['trade_type'] = strtoupper($tradeType);
		return $this;
	}

	/**
	 * 获取支付方式
	 * @return string
	 */
	public function getTradeType() {
		return $this->get('trade_type');
	}

	/**
	 * 设置APP和网页支付提交用户端ip，Native支付填调用微信支付API的机器IP, 必须
	 * @param string $ip ip地址
	 * @return UnifiedOrder $this 返回当前对象进行连贯操作
	 */
	public function setSpbillCreateIp($ip) {
		$this->order['spbill_create_ip'] = $ip;
		return $this;
	}

	/**
	 * 获取APP和网页支付提交用户端ip
	 * @return string
	 */
	public function getSpbillCreateIp() {
		return $this->get('spbill_create_ip');
	}

	/**
	 * 模式二支付必须，模式一支付可选，按需
	 * @param string $notifyUrl, 操作成功后微信回调我司的URL地址
	 * @return UnifiedOrder $this 返回当前对象进行连贯操作
	 */
	public function setNotifyUrl($notifyUrl) {
		$this->order['notify_url'] = $notifyUrl;
		return $this;
	}

	/**
	 * 获取回调参数
	 * @return string
	 */
	public function getNotifyUrl() {
		return $this->get('notify_url');
	}

	/**
	 * 设置用户在商户appid下的唯一标识，trade_type=JSAPI的时候此参数必传，按需
	 * @param string $openId 用户的openid
	 * @return UnifiedOrder $this 返回当前对象进行连贯操作
	 */
	public function setOpenid($openId) {
		$this->order['openid'] = $openId;
		return $this;
	}

	/**
	 * 获取用户在商户appid下的唯一标识
	 * @return string
	 */
	public function getOpenid() {
		return $this->get('openid');
	}

	/**
	 * 设置二维码中包含的商品ID，商户自行定义，trade_type=NATIVE的时候此参数必传，按需
	 * @param number|string $productId 商品id
	 * @return UnifiedOrder $this 返回当前对象进行连贯操作
	 */
	public function setProductId($productId) {
		$this->order['product_id'] = $productId;
		return $this;
	}

	/**
	 * 获取二维码中包含的商品ID
	 * @return number|string
	 */
	public function getProductId() {
		return $this->get('product_id');
	}

	/**
	 * 设置货币种类，默认人民币(CNY)，可选
	 * @param string $feeType 货币种类
	 * @return UnifiedOrder $this 返回当前对象进行连贯操作
	 */
	public function setFeeType($feeType) {
		$this->order['fee_type'] = $feeType;
		return $this;
	}

	/**
	 * 获取货币种类
	 * @return string
	 */
	public function getFeeType() {
		return $this->get('fee_type');
	}

	/**
	 * 设置终端设备号(门店号或收银设备ID)，注意：PC网页或公众号内支付请传"WEB", 可选
	 * @param string $deviceInfo 设备号
	 * @return UnifiedOrder $this 返回当前对象进行连贯操作
	 */
	public function setDeviceInfo($deviceInfo) {
		$this->order['device_info'] = $deviceInfo;
		return $this;
	}

	/**
	 * 获取终端设备号(门店号或收银设备ID)
	 * @return string
	 */
	public function getDeviceInfo() {
		return $this->get('device_info');
	}

	/**
	 * 设置不使用信用卡支付, 可选
	 * @param string $limitPay 只有一个值，no_credit
	 * @return UnifiedOrder $this 返回当前对象进行连贯操作
	 */
	public function setLimitPay($limitPay = 'no_credit') {
		$this->order['limit_pay'] = $limitPay;
		return $this;
	}

	/**
	 * 获取不使用信用卡支付
	 * @return string|null
	 */
	public function getLimitPay() {
		return $this->get('limit_pay');
	}

	/**
	 * 设置商品标记，代金券或立减优惠功能的参数, 可选
	 * @param number|string $goodsTag 商品标记
	 * @return UnifiedOrder $this 返回当前对象进行连贯操作
	 */
	public function setGoodsTag($goodsTag) {
		$this->order['goods_tag'] = $goodsTag;
		return $this;
	}

	/**
	 * 获取商品标记，代金券或立减优惠功能的参数
	 * @return number|string
	 */
	public function getGoodsTag() {
		return $this->get('goods_tag');
	}

	/**
	 * 设置交易生成时间,格式为yyyyMMddHHmmss, 可选
	 * @param string $timeStart 交易生成时间
	 * @return UnifiedOrder $this 返回当前对象进行连贯操作
	 */
	public function setTimeStart($timeStart) {
		$this->order['time_start'] = $timeStart;
		return $this;
	}

	/**
	 * 获取交易生成时间
	 * @return string
	 */
	public function getTimeStart() {
		return $this->get('time_start');
	}

	/**
	 * 设置交易截止时间, 获取订单失效时间，格式为yyyyMMddHHmmss, 最短失效时间间隔必须大于5分钟, 可选
	 * @param string $timeExpire 交易截止时间
	 * @return UnifiedOrder $this 返回当前对象进行连贯操作
	 */
	public function setTimeExpire($timeExpire) {
		$this->order['time_expire'] = $timeExpire;
		return $this;
	}

	/**
	 * 获取交易截止时间
	 * @return string
	 */
	public function getTimeExpire() {
		return $this->get('time_expire');
	}

	/**
	 * 设置商品名称明细列表, 可选
	 * @param string $timeExpire 交易截止时间
	 * @return UnifiedOrder $this 返回当前对象进行连贯操作
	 */
	public function setDetail($detail) {
		$this->order['detail'] = $detail;
		return $this;
	}

	/**
	 * 获取商品名称明细列表
	 * @return string
	 */
	public function getDetail() {
		return $this->get('detail');
	}

	/**
	 * 设置附加数据，在查询API和支付通知中原样返回，该字段主要用于商户携带订单的自定义数据, 可选
	 * @param string $timeExpire 交易截止时间
	 * @return UnifiedOrder $this 返回当前对象进行连贯操作
	 */
	public function setAttach($attach) {
		$this->order['attach'] = $attach;
		return $this;
	}

	/**
	 * 获取附加数据
	 * @return string
	 */
	public function getAttach() {
		return $this->get('attach');
	}

	/**
	 * 将设置过的属性封装到数组
	 * @return array
	 */
	public function toArray() {
		return $this->order;
	}

	/**
	 * 封装get方法，防止notice报错
	 * @param string $key 键名
	 * @param string $default　默认值
	 * @return string|number|null
	 */
	private function get($key, $default = NULL) {
		return isset($this->order[$key]) ? $this->order[$key] : $default;
	}
}