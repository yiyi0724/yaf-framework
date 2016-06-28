<?php

/**
 * 同步回调类，只封装了web|wap共有的参数
 * @author enychen
 */
namespace alisdk\pay;

class Notify {

	/**
	 * 返回参数信息
	 * @var string
	 */
	protected $params = array();

	/**
	 * 构造函数
	 * @return void
	 */
	public function __construct() {
		$this->setParams($_REQUEST);
	}

	/**
	 * 设置支付宝回调参数信息
	 * @param array $params 支付宝回调参数数组
	 * @return Notify $this 返回当前对象进行连贯操作
	 */
	public function setParams(array $params) {
		$this->params = $params;
		return $this;
	}

	/**
	 * 获取所有回调信息
	 * @return array
	 */
	public function getParams() {
		return $this->params;
	}

	/**
	 * 获取签名信息
	 * @return string
	 */
	public function getSign() {
		return $this->get('sign');
	}

	/**
	 * 获取签名方式
	 * @return string DSA|RSA|MD5三个值可选
	 */
	public function getSignType() {
		return $this->get('sign_type');
	}

	/**
	 * 获取支付宝通知校验ID
	 * @return string
	 */
	public function getNotifyId() {
		return $this->get('notify_id');
	}

	/**
	 * 获取通知的时间，默认格式：2014-11-24 00:22:12
	 * @param bool $toTimestamp 是否转成时间戳
	 * @return string|int
	 */
	public function getNotifyTime($toTimestamp = FALSE) {
		$notifyTime = $this->get('notify_time');
		return $toTimestamp ? strtotime($notifyTime) : $notifyTime;
	}

	/**
	 * 获取我司的订单号
	 * @return string
	 */
	public function getOutTradeNo() {
		return $this->get('out_trade_no');
	}

	/**
	 * 获取支付宝的交易号
	 * @return string
	 */
	public function getTradeNo() {
		return $this->get('trade_no');
	}

	/**
	 * 获取交易状态，TRADE_FINISHED-普通即时到账的交易成功状态 | TRADE_SUCCESS-了高级即时到账或机票分销产品后的交易成功状态
	 * @return string TRADE_FINISHED|TRADE_SUCCESS两个个值可选
	 */
	public function getTradeStatus() {
		return $this->get('trade_status');
	}

	/**
	 * 获取交易金额
	 * @return number
	 */
	public function getTotalFee() {
		return $this->get('total_fee');
	}

	/**
	 * 获取透传参数，只针对web付款才有返回
	 * @return string
	 */
	public function getExtraCommonParam() {
		return $this->get('extra_common_param');
	}
	
	/**
	 * 获取卖家信息
	 * @return string
	 */
	public function getSellerId() {
		return $this->get('seller_id');
	}

	/**
	 * 获取买家支付宝账号，wap同步跳转获取不到
	 * @return string
	 */
	public function getBuyerMail() {
		return $this->get('buyer_email');
	}

	/**
	 * 获取买家支付宝账户号对应的唯一用户号，wap同步跳转获取不到
	 * @return string
	 */
	public function getBuyerId() {
		return $this->get('buyer_id');
	}

	/**
	 * 获取交易付款时间，默认格式：2014-11-24 00:22:12，异步回调的时候才能获取到
	 * @param bool $toTimestamp 是否转成时间戳
	 * @return string|int
	 */
	public function getGmtPayMent($toTimestamp = FALSE) {
		$payTime = $this->get('gmt_payment');
		$payTime =  $payTime ? : date('Y-m-d H:i:s');
		return $toTimestamp ? strtotime($payTime) : $payTime;
	}

	/**
	 * 获取回调参数信息
	 * @param string $key 键名
	 * @param string $default 如果获取不到，返回此默认值
	 * @return string
	 */
	public function get($key, $default = '') {
		return isset($this->params[$key]) ? $this->params[$key] : $default;
	}

	/**
	 * 是否异步回调
	 * @return boolean 是的话返回TRUE
	 */
	public function isAsync() {
		return !((bool)$this->get('is_success'));
	}

	/**
	 * 是否同步回调
	 * @return boolean 是的话返回TRUE
	 */
	public function isSync() {
		return (bool)$this->get('is_success');
	}
}