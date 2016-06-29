<?php

/**
 * 微信支付SDK基类
 * @author enychen
 */
namespace weixin;

class Pay extends Base {

	/**
	 * 商户号id
	 * @var string
	 */
	protected $mchid = NULL;

	/**
	 * 商户密钥
	 * @var string
	 */
	protected $key = NULL;

	/**
	 * 代理服务器信息
	 * @var string
	 */
	protected $proxyHost = NULL;

	/**
	 * 代理服务器端口信息
	 * @var int
	 */
	protected $proxyPort = NULL;

	/**
	 * 是否使用证书
	 * @var bool
	 */
	protected $isUseCert = FALSE;

	/**
	 * 创建支付业务逻辑对象
	 * @param string $appid 公众号appid
	 * @param string $mchid 商户id
	 * @param string $key 商户密钥
	 * @return void
	 */
	public function __construct($appid, $mchid, $key) {
		$this->setAppid($appid);
		$this->setMchid($mchid);
		$this->setKey(key);
	}

	/**
	 * 设置商户密钥
	 * @param string $key 密钥串
	 * @return Pay $this 返回当前对象进行连贯操作
	 */
	private function setKey($key) {
		$this->key = $key;
		return $this;
	}

	/**
	 * 获取商户密钥
	 * @return string
	 */
	public function getKey() {
		return $this->key;
	}

	/**
	 * 设置商户id
	 * @param string $mchid 商户id
	 * @return Pay $this 返回当前对象进行连贯操作
	 */
	private function setMchid($mchid) {
		$this->mchid = $mchid;
		return $this;
	}

	/**
	 * 获取商户id
	 * @return string
	 */
	public function getMchid() {
		return $this->mchid;
	}

	/**
	 * 设置代理服务器信息
	 * @param string $proxyHost 理服务器ip地址
	 * @return Pay $this 返回当前对象进行连贯操作
	 */
	public function setProxyHost($proxyHost) {
		$this->proxyHost = $proxyHost;
		return $this;
	}

	/**
	 * 获取代理服务器信息
	 * @return string
	 */
	public function getProxyHost() {
		return $this->proxyHost;
	}

	/**
	 * 设置代理服务器端口信息
	 * @param int $proxyPort 理服务器端口地址
	 * @return Pay $this 返回当前对象进行连贯操作
	 */
	public function setProxyPort($proxyPort) {
		$this->proxyPort = $proxyPort;
		return $this;
	}

	/**
	 * 代理服务器端口信息
	 * @return number
	 */
	public function getProxyPort() {
		return $this->proxyPort;
	}

	/**
	 * 设置是否使用ssl证书
	 * @param bool $useCert 是否使用ssl证书
	 * @return Pay $this 返回当前对象进行连贯操作
	 */
	public function setIsUseCert($isUseCert) {
		$this->isUseCert = $isUseCert;
		return $this;
	}

	/**
	 * 获取是否使用ssl证书
	 * @return boolean
	 */
	public function getIsUseCert() {
		return $this->isUseCert;
	}

	/**
	 * 发送get请求
	 * @param string $url 请求地址
	 * @return string
	 */
	private function get($url) {
		$ch = curl_init();
	
		// 初始化设置
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($curl, CURLOPT_TIMEOUT, 500);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, TRUE);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($curl, CURLOPT_URL, $url);
	
		// 如果有配置代理这里就设置代理
		if($this->getProxyHost() && $this->getProxyPort()) {
			curl_setopt($ch, CURLOPT_PROXY, $this->getProxyHost());
			curl_setopt($ch, CURLOPT_PROXYPORT, $this->getProxyPort());
		}
	
		// 设置证书, cert 与 key 分别属于两个.pem文件
		if($this->getIsUseCert()) {
			curl_setopt($ch, CURLOPT_SSLCERTTYPE, 'PEM');
			curl_setopt($ch, CURLOPT_SSLCERT, __DIR__ . '/apiclient_cert.pem');
			curl_setopt($ch, CURLOPT_SSLKEYTYPE, 'PEM');
			curl_setopt($ch, CURLOPT_SSLKEY, __DIR__ . '/apiclient_key.pem');
		}
	
		$result = curl_exec($curl);
		curl_close($curl);
	
		return $result;
	}

	/**
	 * 生成sign签名
	 * @param array $params 原始数据
	 * @return string
	 */
	private function sign($params) {
		// 签名步骤零：过滤非法数据
		foreach($params as $key=>$value) {
			if($key == 'sign' || !$value || is_array($value)) {
				unset($params[$key]);
			}
		}
		// 签名步骤一：按字典序排序参数并生成请求串
		ksort($params);
		$sign = urldecode(http_build_query($params));
		// 签名步骤二：在string后加入KEY
		$sign .= "&key={$this->getKey()}";
		// 签名步骤三：MD5加密
		$sign = md5($sign);
		// 签名步骤四：所有字符转为大写
		$sign = strtoupper($sign);
		// 返回签名
		return $sign;
	}

	/**
	 * 发送post请求
	 * @param string $url  url地址
	 * @param string $params 需要post的xml字符串数据
	 * @return string
	 */
	private function post($url, $params) {
		$ch = curl_init();
	
		// 初始化设置
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_TIMEOUT, 500);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, TRUE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
	
		// 如果有配置代理这里就设置代理
		if($this->getProxyHost() && $this->getProxyPort()) {
			curl_setopt($ch, CURLOPT_PROXY, $this->proxyHost);
			curl_setopt($ch, CURLOPT_PROXYPORT, $this->proxyPort);
		}
	
		// 设置证书, cert 与 key 分别属于两个.pem文件
		if($this->getIsUseCert()) {
			curl_setopt($ch, CURLOPT_SSLCERTTYPE, 'PEM');
			curl_setopt($ch, CURLOPT_SSLCERT, __DIR__ . '/certificate/apiclient_cert.pem');
			curl_setopt($ch, CURLOPT_SSLKEYTYPE, 'PEM');
			curl_setopt($ch, CURLOPT_SSLKEY, __DIR__ . '/certificate/apiclient_key.pem');
		}
	
		// 获取结果
		$result = curl_exec($ch);
		curl_close($ch);
	
		return $result;
	}

	/**
	 * 回调数据进行检查
	 * @param string $xml字符串数据
	 * @return array xml解码后的数组
	 */
	private function verify($result) {
		// 数据来源检查
		if(!$result) {
			$this->throws(1090, '来源非法');
		}
	
		// 把数据转成xml
		$result = $this->xmlDecode($result);
	
		// 签名检查
		if($this->sign($result) !== $result['sign']) {
			$this->throws(1091, '签名不正确');
		}
	
		// 微信方通信是否成功
		if($result['return_code'] != 'SUCCESS') {
			$this->throws(1092, $data['return_msg']);
		}
	
		// 微信业务处理是否失败
		if(isset($result['result_code']) && $result['result_code'] == 'FAIL') {
			$this->throws(1093, $result['err_code_des']);
		}
	
		return $result;
	}

	/**
	 * 统一下单
	 *　@param \weixin\pay\UnifiedOrder $unifiedOrderObject 统一下单对象
	 * @return void
	 */
	public function unifiedOrder(\weixin\pay\UnifiedOrder $unifiedOrderObject) {
		// 必传参数检查
		if(!$unifiedOrderObject->getOutTradeNo()) {
			$this->throws(1000, '请设置订单号');
		} 
		if(!$unifiedOrderObject->getTotalFee()) {
			$this->throws(1001, '请设置价格');
		}
		if(!$unifiedOrderObject->getBody()) {
			$this->throws(1002, '请设置商品描述信息');
		}
		if(!in_array($unifiedOrderObject->getTradeType(), array('JSAPI', 'NATIVE', 'APP', 'WAP'))) {
			$this->throws(1003, "请设置交易类型");
		}
		if(!$unifiedOrderObject->getNotifyUrl()) {
			$this->throws(1003, "请设通知地址");
		}
		// 业务参数检查
		if($unifiedOrderObject->getTradeType() == 'JSAPI' && !$unifiedOrderObject->getOpenid()) {
			$this->throws(1004, '请设置openid');
		}
		if($unifiedOrderObject->getTradeType() == 'NAVITE' && $unifiedOrderObject->getProductId()) {
			$this->throws(1005, '请设置product_id');
		}

		// 支付参数整合
		$order = $unifiedOrderObject->toArray();
		$order['appid'] = $this->getAppid();
		$order['mch_id'] = $this->getMchid();
		$order['nonce_str'] = $this->strShuffle();
		$order['sign'] = $this->sign($order);
	
		// xml编码
		$order = $this->XmlEncode($order);
	
		// curl微信生成订单
		$api = 'https://api.mch.weixin.qq.com/pay/unifiedorder';
		$result = $this->post($api, $order);
		$result = $this->verify($result);

		return $result;
	}

	/**
	 * jsapi支付
	 * @param \weixin\pay\UnifiedOrder $unifiedOrderObject
	 * @return string
	 */
	public function jsPay(\weixin\pay\UnifiedOrder $unifiedOrderObject) {
		$unifiedOrder->setTradeType('JSAPI');
		$unifiedOrder->setDeviceInfo('WEB');
		$result = $this->unifiedOrder($unifiedOrderObject);
	
		// 支付信息，由微信js进行调用
		$this->setTimeStamp(time());
	
		$jsPays['appId'] = $this->getAppid();
		$jsPays['nonceStr'] = $this->strShuffle();
		$jsPays['signType'] = 'MD5';
		$jsPays['package'] = "prepay_id={$result['prepay_id']}";
		$jsPays['paySign'] = $this->sign($jsPays);
	
		return $jsPays;
	}

	/**
	 * 执行微信订单查询
	 * 文档地址：https://pay.weixin.qq.com/wiki/doc/api/jsapi.php?chapter=9_2（普通订单查询）
	 * 文档地址：https://pay.weixin.qq.com/wiki/doc/api/jsapi.php?chapter=9_5（退款订单查询）
	 * @return void
	 */
	public function query() {
		// 检查要查询的订单号
		foreach(array('transaction_id', 'out_trade_no', 'out_refund_no', 'refund_id') as $key) {
			if(!empty($this->query[$key])) {
				$isPass = TRUE;
				break;
			}
		}
		if(empty($isPass)) {
			$this->throws(1010, '请设置订单号');
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

	/**
	 * 关闭订单
	 * @return void
	 */
	public function close(\weixin\pay\Close $closeObject) {
		if(!$closeObject->getOutTradeNo()) {
			$this->throws(1031, '请设置设置订单号');
		}

		$query['out_trade_no'] = $closeObject->getOutTradeNo();
		$query['appid'] = $this->getAppid();
		$query['mch_id'] = $this->getMchid();
		$query['nonce_str'] = $this->strShuffle();
		$query['sign'] = $this->sign($query);

		// xml编码
		$query = $this->XmlEncode($query);

		// curl微信生成订单
		$api = 'https://api.mch.weixin.qq.com/pay/closeorder';
		$result = $this->post($api, $this->close);
		$result = $this->verify($result);

		return $result;
	}

	/**
	 * 执行微信订单退款
	 * 文档地址：https://pay.weixin.qq.com/wiki/doc/api/jsapi.php?chapter=9_4
	 * @return void
	 */
	public function refundOrder() {
		$this->isUseCert(TRUE);
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