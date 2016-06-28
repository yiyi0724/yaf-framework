<?php

/**
 * 支付宝支付公共方法类
 * @author enychen
 * @version 1.0
 */
namespace alisdk;

class Pay {

	/**
	 * 合作者id（appid）
	 * @var string
	 */
	protected $partner;

	/**
	 * 和作者密钥（appkey）
	 * @var string
	 */
	protected $key;

	/**
	 * _input_charset字符编码
	 * @var string
	 */
	protected $inputCharset = 'utf-8';

	/**
	 * 加密方式
	 * @var string
	 */
	protected $signType = 'MD5';

	/**
	 * 服务器异步通知页面路径
	 * @var string
	 */
	protected $notifyUrl = NULL;

	/**
	 * 页面跳转同步通知页面路径
	 * @var string
	 */
	protected $returnUrl = NULL;

	/**
	 * 证书地址
	 * @var string
	 */
	protected $certificate = __DIR__ . '/pay/certificate/cacert.pem';

	/**
	 * 构造函数
	 * @param string $partner 合作者id
	 * @param string $key	  合作者密钥
	 * @return void
	 */
	public function __construct($partner, $key) {
		$this->setPartner($partner)->setKey($key);
	}

	/**
	 * 设置合作者id
	 * @param string $partner 合作者id
	 * @return Pay $this 返回当前对象进行连贯操作
	 */
	public function setPartner($partner) {
		$this->partner = $partner;
		return $this;
	}

	/**
	 * 获取合作者id
	 * @return string
	 */
	public function getPartner() {
		return $this->partner;
	}

	/**
	 * 设置合作者密钥
	 * @param string $key 合作者密钥
	 * @return Pay $this 返回当前对象进行连贯操作
	 */
	public function setKey($key) {
		$this->key = $key;
		return $this;
	}

	/**
	 * 获取合作者密钥
	 * @return string
	 */
	public function getKey() {
		return $this->key;
	}

	/**
	 * 设置字符集编码格式
	 * @param string $inputCharset 字符集编码格式
	 * @return Pay $this 返回当前对象进行连贯操作
	 */
	public function setInputCharset($inputCharset) {
		$this->inputCharset = $inputCharset;
		return $this;
	}

	/**
	 * 获取字符集编码格式
	 * @return string
	 */
	public function getInputCharset() {
		return $this->inputCharset;
	}

	/**
	 * 设置签名加密方式
	 * @param string $signType 加密方式
	 * @return Pay $this 返回当前对象进行连贯操作
	 */
	public function setSignType($signType) {
		$this->signType = strtoupper($signType);
		return $this;
	}

	/**
	 * 获取签名加密方式
	 * @return string
	 */
	public function getSignType() {
		return $this->signType;
	}

	/**
	 * 设置服务器异步通知页面路径
	 * @param string $notifyUrl url地址
	 * @return Pay $this 返回当前对象进行连贯操作
	 */
	public function setNotifyUrl($notifyUrl) {
		$this->notifyUrl = $notifyUrl;
		return $this;
	}

	/**
	 * 获取服务器异步通知页面路径
	 * @return string
	 */
	public function getNotifyUrl() {
		return $this->notifyUrl;
	}

	/**
	 * 设置页面跳转同步通知页面路径
	 * @param string $returnUrl url地址
	 * @return Pay $this 返回当前对象进行连贯操作
	 */
	public function setReturnUrl($returnUrl) {
		$this->returnUrl = $returnUrl;
		return $this;
	}

	/**
	 * 获取页面跳转同步通知页面路径
	 * @return string
	 */
	public function getReturnUrl() {
		return $this->returnUrl;
	}

	/**
	 * 设置证书路径
	 * @param string $certificate 证书完整路径
	 * @return Pay $this 返回当前对象进行连贯操作
	 */
	public function setCertificate($certificate) {
		$this->certificate = $certificate;
		return $this;
	}

	/**
	 * 获取完整证书路径
	 * @return string
	 */
	public function getCertificate() {
		return $this->certificate;
	}

	/**
	 * 数据进行签名
	 * @param array $params 参数列表
	 * @return string 签名字符串
	 */
	private function sign($params) {
		// 删除不进行签名的字段
		foreach($params as $key=>$value) {
			if(in_array($key, array('sign', 'sign_type')) || !$value) {
				unset($params[$key]);
			}
		}
		
		// 进行排序
		ksort($params);

		// 生成加密信息
		switch($this->getSignType()) {
			case 'MD5':
				return md5(urldecode(http_build_query($params)) . $this->getKey());
				break;
			default:
				return NULL;
		}
	}

	/**
	 * 抛出异常信息
	 * @param int $code 异常码
	 * @param code $message 异常信息
	 * @throws Exception
	 */
	public function throws($code, $message) {
		throw new Exception($message, $code);
	}

	/**
	 * 网站支付
	 * @param \alisdk\pay\Web $webObject web支付数据对象
	 * @return array action=>请求地址, params=>请求附加参数数组
	 * @throws \alisdk\Exception
	 */
	public function webPayment(\alisdk\pay\Web $webObject) {
		// 参数检查
		if(!$webObject->getOutTradeNo()) {
			$this->throws(2001, '请设置订单号码');
		}
		if(!$webObject->getSubject()) {
			$this->throws(2002, '请设置商品描述');
		}
		if(!$webObject->getTotalFee()) {
			$this->throws(2003, '请设交易金额');
		}
		if(!$this->getNotifyUrl()) {
			$this->throws(2005, '请设置异步回调地址');
		}
		if(!$this->getReturnUrl()) {
			$this->throws(2006, '请设置同步回调地址');
		}

		// 整合订单信息
		$order = $webObject->getOrder();
		$order['service'] = 'create_direct_pay_by_user';
		$order['seller_id'] = $this->getPartner();
		$order['partner'] = $this->getPartner();
		$order['payment_type'] = 1;
		$order['_input_charset'] = $this->getInputCharset();
		$order['sign_type'] = $this->getSignType();
		$order['notify_url'] = $this->getNotifyUrl();
		$order['return_url'] = $this->getReturnUrl();
		$order['sign'] = $this->sign($order);

		// 组装地址
		$api = 'https://mapi.alipay.com/gateway.do?_input_charset=%s';
		return array('action'=>sprintf($api, $this->getInputCharset()), 'params'=>$order);
	}

	/**
	 * wap新版本支付
	 * @param \alisdk\pay\Wap $wapObject wap支付数据对象
	 * @return array action=>请求地址, params=>请求附加参数数组
	 * @throws \alisdk\Exception
	 */
	public function wapPayment(\alisdk\pay\Wap $wapObject) {
		// 参数检查
		if(!$wapObject->getOutTradeNo()) {
			$this->throws(2007, '请设置订单号码');
		}
		if(!$wapObject->getSubject()) {
			$this->throws(2008, '请设置商品描述');
		}
		if(!$wapObject->getTotalFee()) {
			$this->throws(2009, '请设交易金额');
		}
		if(!$wapObject->getShowUrl()) {
			$this->throws(2010, '请设置商品显示地址');
		}
		if(!$this->getNotifyUrl()) {
			$this->throws(2011, '请设置异步回调地址');
		}
		if(!$this->getReturnUrl()) {
			$this->throws(2012, '请设置同步回调地址');
		}

		// 整合订单信息
		$order = $wapObject->getOrder();
		$order['service'] = 'alipay.wap.create.direct.pay.by.user';
		$order['seller_id'] = $this->getPartner();
		$order['partner'] = $this->getPartner();
		$order['payment_type'] = 1;
		$order['_input_charset'] = $this->getInputCharset();
		$order['sign_type'] = $this->getSignType();
		$order['notify_url'] = $this->getNotifyUrl();
		$order['return_url'] = $this->getReturnUrl();
		$order['sign'] = $this->sign($order);

		// 组装地址
		$api = 'https://mapi.alipay.com/gateway.do?_input_charset=%s';
		return array('action'=>sprintf($api, $this->getInputCharset()), 'params'=>$order);
	}

	/**
	 * 同步|异步回调验证
	 * @param \alisdk\pay\Notify $notifyObject 回调对象
	 * @return void
	 */
	private function verify(\alisdk\pay\Notify $notifyObject) {
		// 签名结果检查
		if(!$notifyObject->getSign() || ($notifyObject->getSign() != $this->sign($notifyObject->getParams()))) {
			$this->throws(2091, '签名不正确');
		}

		// 回调支付宝的验证地址
		$api = 'https://mapi.alipay.com/gateway.do?service=notify_verify&partner=%s&notify_id=%s';
		$ch = curl_init(sprintf($api, $this->getPartner(), $notifyObject->getNotifyId()));
		curl_setopt($ch, CURLOPT_HEADER, 0); // 过滤HTTP头
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // 返回输出结果
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, TRUE); // SSL证书认证
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2); // 严格认证
		curl_setopt($ch, CURLOPT_CAINFO, $this->getCertificate()); // 证书地址
		$result = curl_exec($ch);
		curl_close($ch);
		if(!preg_match("/true$/i", $result)) {
			$this->throws(2092, '订单非法');
		}

		// 异步回调输出success通知支付宝已经收到
		if($notifyObject->isAsync()) {
			echo 'success';
		}
	}
}