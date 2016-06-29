<?php

/**
 * 微信支付退款
 * @author enychen
 */
namespace weixin\pay;

class Refund extends Base {

	/**
	 * 查询数组
	 * @var array
	 */
	private $refund = array();
	
	/**
	 * 终端设备号(门店号或收银设备ID), 可选
	 * @param string $deviceInfo 设备号
	 * @return void
	 */
	public function setDeviceInfo($deviceInfo) {
		$this->order['device_info'] = $deviceInfo;
	}

	/**
	 * 设置微信的订单号，优先使用
	 * @param string $transactionId 订单号
	 * @return void
	 */
	public function setTransactionId($transactionId) {
		$this->refund['transaction_id'] = $transactionId;
	}

	/**
	 * 设置订单号，我司的订单号,out_trade_no和transaction_id二选一
	 * @param string $outTradeNo 订单号
	 * @return void
	 */
	public function setOutTradeNo($outTradeNo) {
		$this->refund['out_trade_no'] = $outTradeNo;
	}

	/**
	 * 退款的订单号，必须
	 * @param string $outRefundNo 退款订单号
	 * @return void
	 */
	public function setOutRefundNo($outRefundNo) {
		$this->refund['out_refund_no'] = $outRefundNo;
	}

	/**
	 * 设置订单的总金额，必须
	 * @param float $totalFee 退款的总金额
	 * @return void
	 */
	public function setTotalFee($totalFee) {
		$this->refund['total_fee'] = $totalFee * 100;
	}

	/**
	 * 设置退款的金额，必须
	 * @param float $refundFee 退款的金额
	 * @return void
	 */
	public function setRefundFee($refundFee) {
		$this->refund['refund_fee'] = $refundFee * 100;
	}

	/**
	 * 设置退款金额的货币类型，可选，默认人民币
	 * @param string $refundFeeType 货币金额
	 * @return void
	 */
	public function setRefundFeeType($refundFeeType) {
		$this->refund['refund_fee_type'] = $refundFeeType;
	}

	/**
	 * 操作员帐号, 必须
	 * @param int $opUserId 操作员账号
	 * @return void
	 */
	public function setOpUserId($opUserId) {
		$this->refund['op_user_id'] = $opUserId;
	}
}