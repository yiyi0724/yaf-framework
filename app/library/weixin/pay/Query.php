<?php

/**
 * 微信订单查询
 * @author enychen
 */
namespace weixin\pay;

class Query {

	/**
	 * 查询数组
	 * @var array
	 */
	private $query = array();

	/**
	 * 设置我司订单号
	 * @param string $outTradeNo 订单号
	 * @return Query $this 返回当前对象进行连贯操作
	 */
	public function setOutTradeNo($outTradeNo) {
		$this->query['out_trade_no'] = $outTradeNo;
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
	 * 设置微信订单号，优先使用
	 * @param string $outTradeNo 订单号
	 * @return Query $this 返回当前对象进行连贯操作
	 */
	public function setTransactionId($transactionId) {
		$this->query['transaction_id'] = $transactionId;
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
	 * 设置微信退款订单号
	 * @param string $outRefundNo 退款订单号
	 * @return Query $this 返回当前对象进行连贯操作
	 */
	public function setOutRefundNo($outRefundNo) {
		$this->query['out_refund_no'] = $outRefundNo;
		return $this;
	}

	/**
	 * 获取微信退款订单号
	 * @return string
	 */
	public function getOutRefundNo() {
		return $this->get('out_refund_no');
	}

	/**
	 * 设置微信生成的退款单号，在申请退款接口有返回
	 * @param string $outRefundNo 退款订单号
	 * @return Query $this 返回当前对象进行连贯操作
	 */
	public function setRefundId($refundId) {
		$this->query['refund_id'] = $refundId;
	}

	/**
	 * 获取微信生成的退款单号
	 * @return string
	 */
	public function getRefundId() {
		return $this->get('refund_id');
	}

	/**
	 * 将设置过的属性封装到数组
	 * @return array
	 */
	public function toArray() {
		return $this->query;
	}
	
	/**
	 * 封装get方法，防止notice报错
	 * @param string $key 键名
	 * @param string $default　默认值
	 * @return string|number|null
	 */
	private function get($key, $default = NULL) {
		return isset($this->query[$key]) ? $this->query[$key] : $default;
	}
}