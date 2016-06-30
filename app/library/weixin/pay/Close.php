<?php

/**
 * 关闭微信订单
 * @author enychen
 */
namespace weixin\pay;

class Close {

	/**
	 * 订单号，我司订单号或者微信订单号
	 * @var string
	 */
	protected $outTradeNo;

	/**
	 * 设置订单号，我司的订单号,out_trade_no和transaction_id二选一
	 * @param string $outTradeNo 订单号
	 * @return Close $this 返回当前对象进行连贯操作
	 */
	public function setOutTradeNo($outTradeNo) {
		$this->outTradeNo = $outTradeNo;
		return $this;
	}

	/**
	 * 获取要查询的订单号
	 * @return string
	 */
	public function getOutTradeNo() {
		return $this->outTradeNo;
	}
}