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
	
	/**
	 * 关闭订单
	 * @return void
	 */
	public function close(\weixin\pay\Close $closeObject) {
		if(!$closeObject->getOutTradeNo()) {
			$this->throws(1031, '请设置设置订单号');
		}
	
		$close['out_trade_no'] = $closeObject->getOutTradeNo();
		$close['appid'] = $this->getAppid();
		$close['mch_id'] = $this->getMchid();
		$close['nonce_str'] = $this->strShuffle();
		$close['sign'] = $this->sign($close);
	
		// xml编码
		$close = $this->XmlEncode($close);
	
		// curl微信生成订单
		$api = 'https://api.mch.weixin.qq.com/pay/closeorder';
		$result = $this->post($api, $close);
		$result = $this->verify($result);
	
		return $result;
	}
}