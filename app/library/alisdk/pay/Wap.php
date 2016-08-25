<?php

/**
 * 手机网站支付(9001)
 */
namespace alisdk\pay;

class Wap extends Alipay {

	/**
	 * 构造函数
	 * @param string $appId 支付宝分配给开发者的应用ID
	 */
	public function __construct($appId) {
		$this->setAppId($appId)->setMethod('alipay.trade.wap.pay')->setProductCode('QUICK_WAP_PAY');
	}

	/**
	 * 设置商品的描述信息，可选
	 * @param string $body 描述信息
	 * @return Wap $this 返回当前对象进行连贯操作
	 */
	public function setBody($body) {
		$this->setBizContent('body', $body);
		return $this;
	}

	/**
	 * 获取商品的描述信息
	 * @return string|null
	 */
	public function getBody() {
		return $this->getBizContent('body');
	}

	/**
	 * 设置商品的标题，必须
	 * @param string $subject 商品的标题
	 * @return Wap $this 返回当前对象进行连贯操作
	 */
	public function setSubject($subject) {
		$this->setBizContent('subject', $subject);
		return $this;
	}

	/**
	 * 获取商品的标题
	 * @return string|null
	 */
	public function getSubject() {
		return $this->getBizContent('subject');
	}

	/**
	 * 商户网站唯一订单号 必须
	 * @param string $outTradeNo 订单号
	 * @return Wap $this 返回当前对象进行连贯操作
	 */
	public function setOutTradeNo($outTradeNo){
		$this->setBizContent('out_trade_no', $outTradeNo);
		return $this;
	}
	
	/**
	 * 获取订单号
	 * @return String
	 */
	public function getOutTradeNo(){
		return $this->getBizContent('out_trade_no');
	}

	/**
	 * 设置订单的过期时间，可选
	 * @param string $timeoutExpress 取值范围：1m～15d，m-分钟，h-小时，d-天，1c-当天
	 * @return Wap $this 返回当前对象进行连贯操作
	 */
	public function setTimeoutExpress($timeoutExpress) {
		$this->setBizContent('timeout_express', $timeoutExpress);
		return $this;
	}

	/**
	 * 获取订单的过期时间
	 * @return string|null
	 */
	public function getTimeoutExpress() {
		return $this->getBizContent('timeout_express');
	}

	/**
	 * 设置订单总金额，必须
	 * @param string $totalAmount 单位为元，精确到小数点后两位，取值范围[0.01,100000000]
	 * @return Wap $this 返回当前对象进行连贯操作
	 */
	public function setTotalAmount($totalAmount) {
		$this->setBizContent('total_amount', $totalAmount);
		return $this;
	}

	/**
	 * 获取订单总金额
	 * @return string|null
	 */
	public function getTotalAmount() {
		return $this->getBizContent('total_amount');	
	}

	/**
	 * 设置用户授权接口信息，可选
	 * @param string $authToken 接口授权标识
	 * @return Wap $this 返回当前对象进行连贯操作
	 */
	public function setAuthToken($authToken) {
		$this->setBizContent('auth_token', $authToken);
		return $this;
	}

	/**
	 * 获取用户授权接口信息
	 * @return string|null
	 */
	public function getAuthToken() {
		return $this->getBizContent('auth_token');
	}

	/**
	 * 设置销售产品码，必须，不设置默认为QUICK_WAP_PAY
	 * @param string $productCode 商家和支付宝签约的产品码
	 * @return Wap $this 返回当前对象进行连贯操作
	 */
	public function setProductCode($productCode) {
		$this->setBizContent('product_code', $productCode);
		return $this;
	}

	/**
	 * 获取销售产品码
	 * @return string|null
	 */
	public function getProductCode() {
		return $this->getBizContent('product_code');
	}

	/**
	 * 进行支付
	 * @param boolean $build 是否拼接成url地址,FALSE则返回一个数组
	 * @return string|array
	 */
	public function payment($build = TRUE){
		// 参数检查
		if(!$this->getOutTradeNo()) {
			$this->throws(90001, '请设置订单号码');
		}
		if(!$this->getSubject()){
			$this->throws(90002, '请设置商品描述');
		}
		if(!$this->getTotalAmount()) {
			$this->throws(90003, '请设置交易金额');
		}
		if(!$this->getNotifyUrl()) {
			$this->throws(90004, '请设置异步回调地址');
		}
		if(!$this->getReturnUrl()) {
			$this->throws(90005, '请设置同步回调地址');
		}
		if(!$this->getBizContent('product_code')) {
			$this->throws(90006, '请设置销售产品码');
		}
		if(!$this->getBizContent()) {
			$this->throws(90007, '请设置订单业务信息');
		}

		// 请求的参数
		list($urlParams, $fieldsParams) = $this->toArray();
		$urlParams['sign'] = $this->createRsa(array_merge($urlParams, $fieldsParams));

		// 拼接完整的url地址
		return sprintf("%s?%s&%s", $this->getApi(), http_build_query($urlParams), http_build_query($fieldsParams));
	}
}