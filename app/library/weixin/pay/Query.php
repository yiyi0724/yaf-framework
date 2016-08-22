<?php

/**
 * 微信订单查询
 * @author enychen
 */
namespace weixin\pay;

class Query extends Base {

	/**
	 * 查询订单的信息
	 * @var string
	 */
	const QUREY_API = 'https://api.mch.weixin.qq.com/pay/orderquery';

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
	 * 执行微信订单查询
	 * @return array 请参考微信查询订单接口 https://pay.weixin.qq.com/wiki/doc/api/native.php?chapter=9_2
	 */
	public function execute() {
		// 必须参数检查
		if(!$this->getOutTradeNo() && !$this->getTransactionId()) {
			$this->throws(1000021, '请设置订单号');
		}

		// 数据准备
		$query = $this->toArray();
		$query['appid'] = $this->getAppid();
		$query['mch_id'] = $this->getMchid();
		$query['nonce_str'] = $this->strShuffle();
		$query['sign'] = $this->sign($query);
		$query = $this->xmlEncode($query);
	
		// 执行curl
		$result = $this->post(self::QUREY_API, $query);
		$this->checkSignature($result);
	
		return $this->xmlDecode($result);
	}
}