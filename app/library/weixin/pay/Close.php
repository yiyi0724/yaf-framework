<?php

/**
 * 关闭微信订单
 * @author enychen
 */
namespace weixin\pay;

class Close extends Pay {

	/**
	 * 查询数组
	 * @var array
	 */
	private $close = array();

	/**
	 * 创建统一下单对象
	 * @param string $appid 公众号appid
	 * @param string $mchid 商户id
	 * @param string $key 商户密钥
	 */
	public function __construct($appid, $mchid, $key) {
		$this->setAppid($appid);
		$this->setMchid($mchid);
		$this->setKey(key);
	}

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
			throw new \weixin\Exception('未设置订单号', 1031);
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