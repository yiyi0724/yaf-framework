<?php

/**
 * 关闭微信订单
 * @author enychen
 */
namespace weixin\pay;

class Close extends Base {

	/**
	 * 关闭接口
	 * @var string
	 */
	const CLOSE_API = 'https://api.mch.weixin.qq.com/pay/closeorder';

	/**
	 * 设置订单号，我司的订单号,out_trade_no和transaction_id二选一
	 * @param string $outTradeNo 订单号
	 * @return Close $this 返回当前对象进行连贯操作
	 */
	public function setOutTradeNo($outTradeNo) {
		$this->info['out_trade_no'] = $outTradeNo;
		return $this;
	}

	/**
	 * 获取要查询的订单号
	 * @return string
	 */
	public function getOutTradeNo() {
		return $this->getInfo('out_trade_no');
	}
	
	/**
	 * 关闭订单
	 * @return void
	 */
	public function close() {
		if(!$this->getOutTradeNo()) {
			$this->throws(1031, '请设置设置订单号');
		}

		// 准备数据
		$close = $this->toArray();
		$close['appid'] = $this->getAppid();
		$close['mch_id'] = $this->getMchid();
		$close['nonce_str'] = $this->strShuffle();
		$close['sign'] = $this->sign($close);
		$close = $this->xmlEncode($close);
	
		// curl微信生成订单
		$result = $this->post(self::CLOSE_API, $close);
		verify($result);
	
		return $this->xmlDecode($result);
	}
}