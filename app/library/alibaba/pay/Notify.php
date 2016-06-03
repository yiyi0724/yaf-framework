<?php

/**
 * 回调通知检查
 * @author enychen
 */
namespace alibaba\pay;

class Notify extends Base {

	/**
	 * 支付宝回调结果参数列表
	 * @var array
	 */
	private $params;

	/**
	 * HTTPS地址验证
	 * @var string
	 */
	protected $api = 'https://mapi.alipay.com/gateway.do?service=notify_verify&partner=%s&notify_id=%s';

	/**
	 * 创建支付回调对象
	 * @param string $partner 合作者id
	 * @param string $key 合作者密钥
	 * @param array $params 回调参数信息
	 * @return void
	 */
	public function __construct($partner, $key, array $params) {
		parent::__construct($partner, $key);
		$this->setParams($params);
	}

	/**
	 * 设置回调的参数
	 * @param array $params 支付宝支付回调参数
	 * @return void
	 */
	public function setParams(array $params) {
		$this->params = $params;
	}

	/**
	 * 获取回调的参数
	 * @return array
	 */
	public function getParams() {
		return $this->params;
	}
	
	/**
	 * 进行验证
	 * @return void
	 */
	public function verify() {
		// 参数检查
		if(empty($this->params) || empty($this->params['sign']) || empty($this->params['notify_id'])){
			$this->throws(2000, '来源非法');
		}

		// 签名结果检查
		if($this->params['sign'] != $this->sign($this->filterParams($this->params))){
			$this->throws(2001, '签名不正确');
		}

		// 回调支付宝的验证地址
		$ch = curl_init(sprintf($this->api, $this->partner, $this->params['notify_id']));
		curl_setopt($ch, CURLOPT_HEADER, 0); // 过滤HTTP头
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // 显示输出结果
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, TRUE); // SSL证书认证
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2); // 严格认证
		curl_setopt($ch, CURLOPT_CAINFO, __DIR__ . '/certificate/cacert.pem'); // 证书地址
		$result = curl_exec($ch);
		curl_close($ch);
		if(!preg_match("/true$/i", $result)){
			$this->throws(2002, '订单非法');
		}
	}

	
}