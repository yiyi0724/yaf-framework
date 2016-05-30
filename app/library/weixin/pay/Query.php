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
	 * 执行微信订单查询
	 * 文档地址：https://pay.weixin.qq.com/wiki/doc/api/jsapi.php?chapter=9_2
	 * @return void
	 */
	public function queryOrder() {
		// 检查要查询的订单号
		if(!$this->query['transaction_id'] && !$this->query['out_trade_no']) {
			throw new \weixin\Exception('请设置微信或者我司的订单号', 1030);
		}
		// 存在微信订单号，则删除我司订单号
		if($this->query['transaction_id']) {
			$this->query['out_trade_no'] = NULL;
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