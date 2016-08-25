<?php

/**
 * 微信订单查询
 * @author enychen
 */
namespace wxsdk\pay;

class QueryRefund extends Base {

	/**
	 * 退款订单查询接口
	 * @var string
	 */
	const QUERY_REFUND_API = 'https://api.mch.weixin.qq.com/pay/refundquery';

	/**
	 * 设置我司订单号
	 * @param string $outTradeNo 订单号
	 * @return Query $this 返回当前对象进行连贯操作
	 */
	public function setOutTradeNo($outTradeNo) {
		$this->info['out_trade_no'] = $outTradeNo;
		return $this;
	}
	
	/**
	 * 获取我司订单号
	 * @return string
	 */
	public function getOutTradeNo() {
		return $this->getInfo('out_trade_no');
	}
	
	/**
	 * 设置微信订单号，优先使用
	 * @param string $transactionId 订单号
	 * @return Query $this 返回当前对象进行连贯操作
	 */
	public function setTransactionId($transactionId) {
		$this->info['transaction_id'] = $transactionId;
		return $this;
	}
	
	/**
	 * 获取微信订单号
	 * @return string
	 */
	public function getTransactionId() {
		return $this->getInfo('transaction_id');
	}

	/**
	 * 设置微信退款订单号
	 * @param string $outRefundNo 退款订单号
	 * @return Query $this 返回当前对象进行连贯操作
	 */
	public function setOutRefundNo($outRefundNo) {
		$this->info['out_refund_no'] = $outRefundNo;
		return $this;
	}

	/**
	 * 获取微信退款订单号
	 * @return string
	 */
	public function getOutRefundNo() {
		return $this->getInfo('out_refund_no');
	}

	/**
	 * 设置微信生成的退款单号，在申请退款接口有返回
	 * @param string $outRefundNo 退款订单号
	 * @return Query $this 返回当前对象进行连贯操作
	 */
	public function setRefundId($refundId) {
		$this->info['refund_id'] = $refundId;
	}

	/**
	 * 获取微信生成的退款单号
	 * @return string
	 */
	public function getRefundId() {
		return $this->getInfo('refund_id');
	}

	/**
	 * 执行微信退款订单查询
	 * @return array https://pay.weixin.qq.com/wiki/doc/api/native.php?chapter=9_5
	 */
	public function execute() {
		// 必须参数检查
		$queryRefund = $this->toArray();
		if(!$queryRefund) {
			$this->throws(1000119, '请设置订单号');
		}
		// 数据参数准备
		$queryRefund['appid'] = $this->getAppid();
		$queryRefund['mch_id'] = $this->getMchid();
		$queryRefund['nonce_str'] = $this->strShuffle();
		$queryRefund['sign'] = $this->sign($query);
		$query = $this->xmlEncode($query);

		// 执行curl
		$result = $this->post(self::QUERY_REFUND_API, $query);
		$this->checkSignature($result);

		return $this->xmlDecode($result);
	}
}