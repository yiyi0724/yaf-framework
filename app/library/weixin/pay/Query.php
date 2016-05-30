<?php

/**
 * 微信订单查询
 * @author enychen
 */
namespace weixin\pay;

class Query extends Pay {

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

	/**
	 * 执行微信订单查询
	 * 文档地址：https://pay.weixin.qq.com/wiki/doc/api/jsapi.php?chapter=9_2（普通订单查询）
	 * 文档地址：https://pay.weixin.qq.com/wiki/doc/api/jsapi.php?chapter=9_5（退款订单查询）
	 * @return void
	 */
	public function queryOrder() {
		// 检查要查询的订单号
		foreach(array('transaction_id', 'out_trade_no', 'out_refund_no', 'refund_id') as $key) {
			if(!empty($this->query[$key])) {
				$isPass = TRUE;
				break;
			}
		}
		if(empty($isPass)) {
			throw new \weixin\Exception('请设置订单号', 1010);
		}

		$this->query['appid'] = $this->appid;
		$this->query['mch_id'] = $this->mchid;
		$this->query['nonce_str'] = $this->strShuffle();
		$this->query['sign'] = $this->sign($this->query);
		
		// xml编码
		$params = $this->XmlEncode($this->query);
		$this->query = array();
		
		// curl微信生成订单
		$result = $this->post(\weixin\API::PAY_ORDER_QUERY, $params);		
		$result = $this->verify($result);
		
		return $result;
	}
}