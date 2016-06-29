<?php

/**
 * 微信支付退款
 * @author enychen
 */
namespace weixin\pay;

class Refund {

	/**
	 * 查询数组
	 * @var array
	 */
	private $refund = array();
	
	/**
	 * 设置终端设备号(门店号或收银设备ID), 可选
	 * @param string $deviceInfo 设备号
	 * @return Refund $this 返回当前对象进行连贯操作
	 */
	public function setDeviceInfo($deviceInfo) {
		$this->refund['device_info'] = $deviceInfo;
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
	 * 设置微信订单号，优先使用
	 * @param string $transactionId 订单号
	 * @return Refund $this 返回当前对象进行连贯操作
	 */
	public function setTransactionId($transactionId) {
		$this->refund['transaction_id'] = $transactionId;
		return $this;
	}

	/**
	 * 获取微信订单号
	 * @return string
	 */
	public function getTransactionId() {
		return $this->get('transaction_id');
	}

	/**
	 * 设置我司订单号,out_trade_no和transaction_id二选一
	 * @param string $outTradeNo 订单号
	 * @return Refund $this 返回当前对象进行连贯操作
	 */
	public function setOutTradeNo($outTradeNo) {
		$this->refund['out_trade_no'] = $outTradeNo;
		return $this;
	}

	/**
	 * 获取我司订单号
	 * @return string
	 */
	public function getOutTradeNo() {
		return $this->get('out_trade_no');
	}

	/**
	 * 设置退款订单号，必须
	 * @param string $outRefundNo 退款订单号
	 * @return Refund $this 返回当前对象进行连贯操作
	 */
	public function setOutRefundNo($outRefundNo) {
		$this->refund['out_refund_no'] = $outRefundNo;
		return $this;
	}

	/**
	 * 获取退款订单号
	 * @return string
	 */
	public function getOutRefundNo() {
		return $this->get('out_refund_no');
	}

	/**
	 * 设置订单的总金额，必须
	 * @param number $totalFee 退款的总金额
	 * @return Refund $this 返回当前对象进行连贯操作
	 */
	public function setTotalFee($totalFee) {
		$this->refund['total_fee'] = $totalFee * 100;
		return $this;
	}

	/**
	 * 获取订单的总金额
	 * @return number
	 */
	public function getTotalFee() {
		$totalFee = $this->get('total_fee', 0);
		return $totalFee/100;
	}

	/**
	 * 设置退款的金额（内部会转化成分），必须
	 * @param number $refundFee 退款的金额，单位：元
	 * @return Refund $this 返回当前对象进行连贯操作
	 */
	public function setRefundFee($refundFee) {
		$this->refund['refund_fee'] = $refundFee * 100;
		return $this;
	}

	/**
	 * 获取退款的金额
	 * @return number
	 */
	public function getRefundFee() {
		$totalFee = $this->get('refund_fee', 0);
		return $totalFee/100;
	}
	
	/**
	 * 设置退款金额的货币类型，可选，默认人民币
	 * @param string $refundFeeType 货币金额
	 * @return Refund $this 返回当前对象进行连贯操作
	 */
	public function setRefundFeeType($refundFeeType) {
		$this->refund['refund_fee_type'] = $refundFeeType;
		return $this;
	}

	/**
	 * 获取退款金额的货币类型
	 * @return string
	 */
	public function setRefundFeeType() {
		return $this->get('refund_fee_type');
	}

	/**
	 * 设置操作员帐号, 必须
	 * @param int $opUserId 操作员账号
	 * @return Refund $this 返回当前对象进行连贯操作
	 */
	public function setOpUserId($opUserId) {
		$this->refund['op_user_id'] = $opUserId;
		return $this;
	}

	/**
	 * 获取操作员帐号
	 * @return string
	 */
	public function getOpUserId() {
		return $this->get('op_user_id');
	}

	/**
	 * 将设置过的属性封装到数组
	 * @return array
	 */
	public function toArray() {
		return $this->refund;
	}

	/**
	 * 封装get方法，防止notice报错
	 * @param string $key 键名
	 * @param string $default　默认值
	 * @return string|number|null
	 */
	private function get($key, $default = NULL) {
		return isset($this->refund[$key]) ? $this->refund[$key] : $default;
	}
}