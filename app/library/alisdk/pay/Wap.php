<?php

/**
 * 手机网站即时支付（新版）
 * @author enychen
 * @version 1.0
 */
namespace alisdk\pay;

class Wap {

	/**
	 * 订单信息
	 * @var array
	 */
	private $order = array();

	/**
	 * 商户网站唯一订单号，必须
	 * @param string $outTradeNo 订单号
	 * @return Wap $this 返回当前对象进行连贯操作
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
	 * 设置商品名称，必须
	 * @param string $subject 商品名称
	 * @return Wap $this 返回当前对象进行连贯操作
	 */
	public function setSubject($subject) {
		$this->order['subject'] = $subject;
		return $this;
	}

	/**
	 * 获取商品信息
	 * @return string
	 */
	public function getSubject() {
		return $this->get('subject');
	}

	/**
	 * 设置交易金额，必须
	 * @param number $totalFee 价格，单位：人民币，元
	 * @return Wap $this 返回当前对象进行连贯操作
	 */
	public function setTotalFee($totalFee) {
		$this->order['total_fee'] = $totalFee;
		return $this;
	}

	/**
	 * 获取交易金额
	 * @return number
	 */
	public function getTotalFee() {
		return $this->get('total_fee');
	}

	/**
	 * 设置商品展示网址，必须
	 * @param string $showUrl 链接地址
	 * @return Wap $this 返回当前对象进行连贯操作
	 */
	public function setShowUrl($showUrl) {
		$this->order['show_url'] = $showUrl;
		return $this;
	}

	/**
	 * 获取商品展示网址
	 * @return string
	 */
	public function getShowUrl() {
		return $this->get('show_url');
	}

	/**
	 * 设置商品描述，可选
	 * @param string $body 描述信息
	 * @return Wap $this 返回当前对象进行连贯操作
	 */
	public function setBody($body) {
		$this->order['body'] = $body;
		return $this;
	}

	/**
	 * 获取商品描述
	 * @return string
	 */
	public function getBody() {
		return $this->get('body');
	}

	/**
	 * 设置未付款交易的超时时间，一旦超时，该笔交易就会自动被关闭，可选
	  	取值范围：1m～15d。
	  	m-分钟，h-小时，d-天，1c-当天（1c-当天的情况下，无论交易何时创建，都在0点关闭）。
	  	该参数数值不接受小数点，如1.5h，可转换为90m。
	  	当用户输入支付密码、点击确认付款后（即创建支付宝交易后）开始计时。
	  	支持绝对超时时间，格式为yyyy-MM-dd HH:mm。
	 * @param string $itBPay 超时时间，具体格式查看上述描述
	 * @return Wap $this 返回当前对象进行连贯操作
	 */
	public function setItBPay($itBPay) {
		$this->order['it_b_pay'] = $itBPay;
		return $this;
	}

	/**
	 * 获取超时时间
	 * @return string
	 */
	public function getItBPay() {
		return $this->get('it_b_pay');
	}

	/**
	 * 设置钱包token，可选
		接入极简版wap收银台时支持。
		当商户请求是来自支付宝钱包，在支付宝钱包登录后，有生成登录信息token时，使用该参数传入token将可以实现信任登录收银台，不需要再次登录。
		登录后用户还是有入口可以切换账户，不能使用该参数锁定用户。
	 * @param string $externToken 钱包token
	 * @return Wap $this 返回当前对象进行连贯操作
	 */
	public function setExternToken($externToken) {
		$this->order['extern_token'] = $externToken;
		return $this;
	}

	/**
	 * 获取钱包token
	 * @return string
	 */
	public function getExternToken() {
		return $this->get('extern_token');
	}

	/**
	 * 设置航旅订单其它费用，可选
		航旅订单中除去票面价之外的费用，单位为RMB-Yuan。取值范围为[0.01,100000000.00]，精确到小数点后两位。
	 * @param number $otherFee 航旅订单其它费用，单位：人民币，元
	 * @return Wap $this 返回当前对象进行连贯操作
	 */
	public function setOtherFee($otherFee) {
		$this->order['otherfee'] = $otherFee;
		return $this;
	}

	/**
	 * 获取航旅订单其它费用
	 * @return number
	 */
	public function getOtherFee() {
		return $this->get('otherfee');
	}

	/**
	 * 设置航旅订单金额和描述信息，可选
		航旅订单金额描述，由四项或两项构成，各项之间由“|”分隔，每项包含金额与描述，
	   	金额与描述间用“^”分隔，票面价之外的价格之和必须与otherfee相等。
	 * @param string $airticket 航旅订单金额和描述信息
	 * @return Wap $this 返回当前对象进行连贯操作
	 */
	public function setAirticket($airticket) {
		$this->order['airticket'] = $airticket;
		return $this;
	}

	/**
	 * 获取航旅订单金额和描述信息
	 * @return string
	 */
	public function getAirticket() {
		return $this->get('airticket');
	}

	/**
	 * 设置是否发起实名校验，可选
	 * @param string $rnCheck T-发起实名校验；F-不发起实名校验
	 * @return Wap $this 返回当前对象进行连贯操作
	 */
	public function setRnCheck($rnCheck) {
		$this->order['rn_check'] = $rnCheck;
		return $this;
	}

	/**
	 * 获取是否发起实名校验
	 * @return string
	 */
	public function getRnCheck() {
		return $this->get('rn_check');
	}

	/**
	 * 设置买家证件号码，可选
	   	买家证件号码（需要与支付宝实名认证时所填写的证件号码一致）
	    当scene=ZJCZTJF的情况下，才会校验buyer_cert_no字段
	 * @param string $buyerCertNo 证件号码
	 * @return Wap $this 返回当前对象进行连贯操作
	 */
	public function setBuyerCertNo($buyerCertNo) {
		$this->order['buyer_cert_no'] = $buyerCertNo;
		return $this;
	}

	/**
	 * 获取买家证件号码
	 * @return string
	 */
	public function getBuyerCertNo() {
		return $this->get('buyer_cert_no');
	}

	/**
	 * 设置买家真实姓名，可选
		当scene=ZJCZTJF的情况下，才会校验buyer_real_name字段
	 * @param string $buyerRealName 姓名
	 * @return Wap $this 返回当前对象进行连贯操作
	 */
	public function setBuyerRealName($buyerRealName) {
		$this->order['buyer_real_name'] = $buyerRealName;
		return $this;
	}

	/**
	 * 获取买家真实姓名
	 * @return　string
	 */
	public function getBuyerRealName() {
		return $this->get('buyer_real_name');
	}

	/**
	 * 设置收单场景，可选
		收单场景。如需使用该字段，需向支付宝申请开通，否则传入无效
	 * @param string $scene 收单场景
	 * @return Wap $this 返回当前对象进行连贯操作
	 */
	public function setScene($scene) {
		$this->order['scene'] = $scene;
		return $this;
	}

	/**
	 * 获取收单场景
	 * @return string
	 */
	public function getScene() {
		return $this->get('scene');
	}

	/**
	 * 设置花呗分期参数，可选，Json格式。
		hb_fq_num：花呗分期数，比如分3期支付；
		hb_fq_seller_percent：卖家承担收费比例，比如100代表卖家承担100%。
		两个参数必须一起传入。
		具体花呗分期期数和卖家承担收费比例可传入的数值请咨询支付宝。
	 * @param String $hbFqParam json字符串，内容参照上述
	 * @return Wap $this 返回当前对象进行连贯操作
	 */
	public function setHbFqParam($hbFqParam) {
		$this->order['hb_fq_param'] = $hbFqParam;
		return $this;
	}

	/**
	 * 获取花呗分期参数
	 * @return string
	 */
	public function getHbFqParam() {
		return $this->get('hb_fq_param');
	}

	/**
	 * 设置商品类型，不传默认为实物类商品，可选
	 * @param int $goodsType 商品类型，1-实物类商品；0-虚拟类商品
	 * @return Wap $this 返回当前对象进行连贯操作
	 */
	public function setGoodsType($goodsType) {
		$this->order['goods_type'] = $goodsType;
		return $this;
	}

	/**
	 * 获取商品类型
	 * @return int
	 */
	public function getGoodsType() {
		return $this->get('goods_type');
	}

	/**
	 * 将设置过的属性封装成数组
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