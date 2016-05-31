<?php

/**
 * 关闭微信订单
 * @author enychen
 */
namespace weixin\pay;

class Close extends Base {

	/**
	 * 查询数组
	 * @var array
	 */
	private $close = array();

	/**
	 * 设置订单号，我司的订单号,out_trade_no和transaction_id二选一
	 * @param string $outTradeNo 订单号
	 * @return void
	 */
	public function setOutTradeNo($outTradeNo) {
		$this->close['out_trade_no'] = $outTradeNo;
	}

	/**
	 * 执行微信订单查询
	 * 文档地址：https://pay.weixin.qq.com/wiki/doc/api/jsapi.php?chapter=9_3
	 * @return void
	 */
	public function closeOrder() {
		// 存在微信订单号，则删除我司订单号
		if($this->close['out_trade_no']) {
			$this->throws(1031, '请设置设置订单号');
		}
		
		$this->close['appid'] = $this->appid;
		$this->close['mch_id'] = $this->mchid;
		$this->close['nonce_str'] = $this->strShuffle();
		$this->close['sign'] = $this->sign($this->close);
		
		// xml编码
		$this->close = $this->XmlEncode($this->close);
		
		// curl微信生成订单
		$result = $this->post(\weixin\API::PAY_CLOSE_ORDER, $this->close);
		$result = $this->verify($result);
		
		return $result;
	}
}