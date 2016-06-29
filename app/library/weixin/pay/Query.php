<?php

/**
 * 微信订单查询
 * @author enychen
 */
namespace weixin\pay;

class Query extends Base {

	/**
	 * 查询数组
	 * @var array
	 */
	private $query = array();

	/**
	 * 设置订单号，我司的订单号,out_trade_no和transaction_id二选一
	 * @param string $outTradeNo 订单号
	 * @return void
	 */
	public function setOutTradeNo($outTradeNo) {
		$this->query['out_trade_no'] = $outTradeNo;
	}

	/**
	 * 设置微信的订单号，优先使用
	 * @param string $outTradeNo 订单号
	 * @return void
	 */
	public function setTransactionId($transactionId) {
		$this->query['transaction_id'] = $transactionId;
	}

	/**
	 * 商户侧传给微信的退款单号，必须
	 * @param string $outRefundNo 退款订单号
	 * @return void
	 */
	public function setOutRefundNo($outRefundNo) {
		$this->refund['out_refund_no'] = $outRefundNo;
	}

	/**
	 * 微信生成的退款单号，在申请退款接口有返回，必须
	 * @param string $outRefundNo 退款订单号
	 * @return void
	 */
	public function setRefundId($refundId) {
		$this->refund['refund_id'] = $refundId;
	}
}