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
	 * 创建支付业务逻辑对象
	 * @param string $appid 公众号appid
	 * @param string $mchid 商户id
	 * @param string $key 商户密钥
	 * @return void
	 */
	public function __construct($appid, $mchid, $key) {
		parent::__construct($appid, $mchid, $key);
		$this->setIsUseCert(TRUE);
	}
	
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

	/**
	 * 执行微信订单退款
	 * 文档地址：https://pay.weixin.qq.com/wiki/doc/api/jsapi.php?chapter=9_4
	 * @return void
	 */
	public function refundOrder() {
		// 检查要查询的订单号
		if(empty($this->refund['transaction_id']) && empty($this->refund['out_trade_no'])) {
			$this->throws(1020, '请设置微信或者我司的订单号');
		}
		// 订单退款号检查
		if(empty($this->refund['out_refund_no'])) {
			$this->throws(1021, '请设置退款订单号');
		}
		// 总金额检查
		if(empty($this->refund['total_fee'])) {
			$this->throws(1022, '请设置总金额');
		}
		// 退款金额检查
		if(empty($this->refund['refund_fee'])) {
			$this->throws(1023, '请设置退款金额');
		}
		// 操作人员检查
		if(empty($this->refund['op_user_id'])) {
			$this->throws(1024, '请设置操作人员信息');
		}

		// 拼接公共参数
		$this->refund['appid'] = $this->appid;
		$this->refund['mch_id'] = $this->mchid;
		$this->refund['nonce_str'] = $this->strShuffle();
		$this->refund['sign'] = $this->sign($this->refund);

		// xml编码
		$this->refund = $this->XmlEncode($this->refund);

		// curl微信生成订单
		$result = $this->post(\weixin\API::PAY_REFUND, $this->refund);
		$result = $this->verify($result);

		return $result;
	}
}