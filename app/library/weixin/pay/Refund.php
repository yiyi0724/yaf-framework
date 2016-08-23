<?php

/**
 * 微信支付退款
 * @author enychen
 */
namespace weixin\pay;

class Refund extends Base {
	
	/**
	 * 支付退款接口
	 * @var string
	 */
	const REFUND_API = 'https://api.mch.weixin.qq.com/secapi/pay/refund';
	
	/**
	 * 设置终端设备号(门店号或收银设备ID), 可选
	 * @param string $deviceInfo 设备号
	 * @return Refund $this 返回当前对象进行连贯操作
	 */
	public function setDeviceInfo($deviceInfo) {
		$this->info['device_info'] = $deviceInfo;
		return $this;
	}

	/**
	 * 获取终端设备号(门店号或收银设备ID)
	 * @return string
	 */
	public function getDeviceInfo() {
		return $this->getInfo('device_info');
	}

	/**
	 * 设置微信订单号，优先使用
	 * @param string $transactionId 订单号
	 * @return Refund $this 返回当前对象进行连贯操作
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
	 * 设置我司订单号,out_trade_no和transaction_id二选一
	 * @param string $outTradeNo 订单号
	 * @return Refund $this 返回当前对象进行连贯操作
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
	 * 设置退款订单号，必须
	 * @param string $outRefundNo 退款订单号
	 * @return Refund $this 返回当前对象进行连贯操作
	 */
	public function setOutRefundNo($outRefundNo) {
		$this->info['out_refund_no'] = $outRefundNo;
		return $this;
	}

	/**
	 * 获取退款订单号
	 * @return string
	 */
	public function getOutRefundNo() {
		return $this->getInfo('out_refund_no');
	}

	/**
	 * 设置订单的总金额（内部会自动转成分），必须
	 * @param number $totalFee 退款的总金额，单位：元
	 * @return Refund $this 返回当前对象进行连贯操作
	 */
	public function setTotalFee($totalFee) {
		$this->info['total_fee'] = $totalFee * 100;
		return $this;
	}

	/**
	 * 获取订单的总金额
	 * @return number
	 */
	public function getTotalFee() {
		$totalFee = $this->getInfo('total_fee');
		return $totalFee/100;
	}

	/**
	 * 设置退款的金额（内部会转化成分），必须
	 * @param number $refundFee 退款的金额，单位：元
	 * @return Refund $this 返回当前对象进行连贯操作
	 */
	public function setRefundFee($refundFee) {
		$this->info['refund_fee'] = $refundFee * 100;
		return $this;
	}

	/**
	 * 获取退款的金额
	 * @return number
	 */
	public function getRefundFee() {
		$totalFee = $this->getInfo('refund_fee');
		return $totalFee/100;
	}
	
	/**
	 * 设置退款金额的货币类型，可选，默认人民币
	 * @param string $refundFeeType 货币金额
	 * @return Refund $this 返回当前对象进行连贯操作
	 */
	public function setRefundFeeType($refundFeeType) {
		$this->info['refund_fee_type'] = $refundFeeType;
		return $this;
	}

	/**
	 * 获取退款金额的货币类型
	 * @return string
	 */
	public function setRefundFeeType() {
		return $this->getInfo('refund_fee_type');
	}

	/**
	 * 设置操作员帐号, 必须
	 * @param int $opUserId 操作员账号
	 * @return Refund $this 返回当前对象进行连贯操作
	 */
	public function setOpUserId($opUserId) {
		$this->info['op_user_id'] = $opUserId;
		return $this;
	}

	/**
	 * 获取操作员帐号
	 * @return string
	 */
	public function getOpUserId() {
		return $this->getInfo('op_user_id');
	}

	/**
	 * 执行微信订单退款
	 * @return void
	 */
	public function execute() {
		// 检查要查询的订单号
		if(!$this->getTransactionId() && !$this->getOutTradeNo()) {
			$this->throws(1000041, '请设置微信或者我司的订单号');
		}
		// 订单退款号检查
		if(!$this->getOutRefundNo()) {
			$this->throws(1000042, '请设置退款订单号');
		}
		// 总金额检查
		if(!$this->getTotalFee()) {
			$this->throws(1000043, '请设置总金额');
		}
		// 退款金额检查
		if(!$this->getRefundFee()) {
			$this->throws(1000044, '请设置退款金额');
		}
		// 操作人员检查
		if(!$this->getOpUserId()) {
			$this->throws(1000045, '请设置操作人员信息');
		}
	
		// 拼接公共参数
		$refund = $this->toArray();
		$refund['appid'] = $this->getAppid();
		$refund['mch_id'] = $this->getMchid();
		$refund['nonce_str'] = $this->strShuffle();
		$refund['sign'] = $this->sign($refund);
	
		// xml编码
		$refund = $this->xmlEncode($refund);

		// 必须使用双向证书
		$this->setUseCert();
		$result = $this->post(self::REFUND_API, $refund);
		$this->verify($result);

		return $this->xmlDecode($result);
	}
	
}