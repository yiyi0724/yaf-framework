<?php

/**
 * 支付宝(9000)
 */
namespace alisdk\pay;

class Base {

	/**
	 * 正式上线api支付接口
	 * @var string
	 */
	const API = 'https://openapi.alipay.com/gateway.do';

	/**
	 * 开发测试api支付接口
	 * @var string
	 */
	const API_DEV = 'https://openapi.alipaydev.com/gateway.do';

	/**
	 * 支付宝分配给开发者的应用ID
	 * @var string
	 */
	protected $appId = NULL;

	/**
	 * 密钥
	 * @var string
	 */
	protected $secret = 'E1IoplulQWXsSDUgWREgQA==';

	/**
	 * 接口名称
	 * @var string
	 */
	protected $method = NULL;

	/**
	 * 参数编码集
	 * @var string
	 */
	protected $charset = 'UTF-8';

	/**
	 * 格式内容，仅支持JSON
	 * @var string
	 */
	protected $format = 'json';

	/**
	 * 签名方式
	 * @var string
	 */
	protected $signType = 'RSA';

	/**
	 * 发送请求的时间，格式"yyyy-MM-dd HH:mm:ss"
	 * @var string
	 */
	protected $timestamp = NULL;

	/**
	 * 调用的接口版本，固定为：1.0
	 * @var string
	 */
	protected $version = '1.0';

	/**
	 * 同步url地址
	 * @var string
	 */
	protected $returnUrl = NULL;

	/**
	 * 异步url地址
	 * @var string
	 */
	protected $notifyUrl = NULL;

	/**
	 * 请求参数的集合
	 * @var array
	 */
	protected $bizContent = array();

	/**
	 * 是否调试状态
	 * @var boolean
	 */
	protected $debug = FALSE;

	/**
	 * 我司rsa私钥
	 * @var string
	 */
	protected $rsaPrivateKeyPath = NULL;

	/**
	 * 支付宝rsa公钥
	 * @var string
	 */
	protected $rsaPublicKeyPath = NULL;

	/**
	 * 设置支付宝分配给开发者的应用ID，必须
	 * @param string $appId 合作者id
	 * @return Alipay $this 返回当前对象进行连贯操作
	 */
	protected function setAppId($appId) {
		$this->appId = $appId;
		return $this;
	}

	/**
	 * 获取支付宝分配给开发者的应用ID
	 * @return string
	 */
	public function getAppId() {
		return $this->appId;
	}

	/**
	 * 设置密钥，可选，不设置使用默认属性值
	 * @param string $secret 密钥
	 * @return Alipay $this 返回当前对象进行连贯操作
	 */
	public function setSecret($secret) {
		$this->secret = $secret;
		return $this;
	}

	/**
	 * 获取密钥
	 * @return string
	 */
	public function getSecret() {
		return $this->secret;
	}

	/**
	 * 设置接口名称，必须
	 * @param string $method 接口名称
	 * @return Alipay $this 返回当前对象进行连贯操作
	 */
	protected function setMethod($method) {
		$this->method = $method;
		return $this;
	}

	/**
	 * 获取接口名称
	 * @return string
	 */
	public function getMethod() {
		return $this->method;
	}

	/**
	 * 设置请求使用的编码格式，必须，不设置默认为utf-8
	 * @param string $charset 字符集名称，如utf-8,gbk,gb2312等
	 * @return Alipay $this 返回当前对象进行连贯操作
	 */
	public function setCharset($charset) {
		$this->charset = $charset;
		return $this;
	}

	/**
	 * 获取请求使用的编码格式
	 * @return string
	 */
	public function getCharset() {
		return $this->charset;
	}

	/**
	 * 设置格式方式，可选，不设置默认为JSON
	 * @param string $method 接口名称
	 * @return Alipay $this 返回当前对象进行连贯操作
	 */
	protected function setFormat($format) {
		$this->format = $format;
		return $this;
	}

	/**
	 * 获取格式方式
	 * @return string
	 */
	public function getFormat() {
		return $this->format;
	}

	/**
	 * 设置商户生成签名字符串所使用的签名算法类型，必须，目前只有支持RSA，不设置默认为RSA
	 * @param string $signTypes 加密算法
	 * @return Alipay $this 返回当前对象进行连贯操作
	 */
	public function setSignType($signType) {
		$this->signType = $signType;
		return $this;
	}

	/**
	 * 获取商户生成签名字符串所使用的签名算法类型
	 * @return string
	 */
	public function getSignType() {
		return $this->signType;
	}

	/**
	 * 设置时间，必须，不设置默认为当前的 Y-m-d h:i:s 格式
	 * @param string $timestamp 时间格式为Y-m-d H:i:s
	 * @return Alipay $this 返回当前对象进行连贯操作
	 */
	public function setTimestamp($timestamp) {
		$this->timestamp = $timestamp;
		return $this;
	}
	
	/**
	 * 获取时间
	 * @return string
	 */
	public function getTimestamp() {
		return $this->timestamp ? : date('Y-m-d H:i:s');
	}

	/**
	 * 设置接口版本，必须，不设置默认为1.0
	 * @param string $version 版本号
	 * @return Alipay $this 返回当前对象进行连贯操作
	 */
	public function setVersion($version) {
		$this->version = $version;
		return $this;
	}
	
	/**
	 * 获取接口版本
	 * @return string
	 */
	public function getVersion() {
		return $this->version;
	}	

	/**
	 * 设置跳转地址，场景可选，手机网站和即时到帐支付必须，app支付可选
	 * @param string $returnUrl url地址
	 * @return Alipay $this 返回当前对象进行连贯操作
	 */
	public function setReturnUrl($returnUrl) {
		$this->returnUrl = $returnUrl;
		return $this;
	}

	/**
	 * 获取跳转地址
	 * @return string
	 */
	public function getReturnUrl() {
		return $this->returnUrl;
	}

	/**
	 * 设置异步通知地址，必须
	 * @param string $notifyUrl url地址
	 * @return Alipay $this 返回当前对象进行连贯操作
	 */
	public function setNotifyUrl($notifyUrl) {
		$this->notifyUrl = $notifyUrl;
		return $this;
	}

	/**
	 * 获取异步通知地址
	 * @return string
	 */
	public function getNotifyUrl() {
		return $this->notifyUrl;
	}

	/**
	 * 设置业务参数信息，必须
	 * @param string $key 键
	 * @param string $value 值
	 * @return Alipay $this 返回当前对象进行连贯操作
	 */
	protected function setBizContent($key, $value) {
		$this->bizContent[$key] = $value;
		return $this;
	}

	/**
	 * 获取业务参数信息
	 * @param string $key 键
	 * @return string|null 找到返回具体值，找不到返回NULL，如果未设置查找键，则返回整个数组
	 */
	protected function getBizContent($key = NULL) {
		if($key) {
			$value = isset($this->bizContent[$key]) ? $this->bizContent[$key] : NULL;
		} else {
			$value = $this->bizContent;
			ksort($value);
		}

		return $value;
	}

	/**
	 * 设置设置调试模式
	 * @param boolean $isDebug 调试模式，默认开启
	 * @return Alipay $this 返回当前对象进行连贯操作
	 */
	public function setDebug($debug = TRUE) {
		$this->debug = $debug;
		return $this;
	}

	/**
	 * 获取设置调试模式状态
	 * @return boolean
	 */
	public function getDebug() {
		return $this->debug;
	}

	/**
	 * 设置我司私钥路径
	 * @param string $rsaPrivateKeyPath 密钥路径
	 * @return Alipay $this 返回当前对象进行连贯操作
	 */
	public function setRsaPrivateKeyPath($rsaPrivateKeyPath) {
		$this->rsaPrivateKeyPath = $rsaPrivateKeyPath;
		return $this;
	}

	/**
	 * 获取我司私钥路径
	 * @return string
	 */
	public function getRsaPrivateKeyPath() {
		return $this->rsaPrivateKeyPath ? : sprintf("%s/certificate/rsa_private_key.pem", __DIR__);
	}

	/**
	 * 设置支付宝公钥路径
	 * @param string $rsaPublicKeyPath 公钥路径
	 * @return Alipay $this 返回当前对象进行连贯操作
	 */
	public function setRsaPublicKeyPath($rsaPublicKeyPath) {
		$this->rsaPublicKeyPath = $rsaPublicKeyPath;
		return $this;
	}

	/**
	 * 获取支付宝公钥路径
	 * @return string
	 */
	public function getRsaPublicKeyPath() {
		return $this->rsaPublicKeyPath ? : sprintf("%s/certificate/alipay_rsa_public_key.pem", __DIR__);
	}

	/**
	 * 获取api地址
	 * @return string
	 */
	public function getApi() {
		return $this->getDebug() ? static::API_DEV : static::API;
	}

	/**
	 * 属性转成数组
	 */
	public function toArray() {
		// 公共参数
		$urlParams['app_id'] = $this->getAppId();
		$urlParams['method'] = $this->getMethod();
		$urlParams['format'] = $this->getFormat();
		$urlParams['charset'] = $this->getCharset();
		$urlParams['sign_type'] = $this->getSignType();
		$urlParams['timestamp'] = $this->getTimestamp();
		$urlParams['version'] = $this->getVersion();
		$urlParams['notify_url'] = $this->getNotifyUrl();
		$urlParams['return_url'] = $this->getReturnUrl();
		
		// 业务参数
		$fieldsParams['biz_content'] = json_encode($this->getBizContent(), JSON_UNESCAPED_UNICODE);
		
		return array($urlParams, $fieldsParams);
	}

	/**
	 * rsa加密
	 * @param array $params 数据数组
	 * @return string 加密串
	 */
	public function createRsa($params) {
		ksort($params);
		$params = urldecode(http_build_query($params));
		$privateKey = file_get_contents($this->getRsaPrivateKeyPath());
		$pkeyid = openssl_get_privatekey($privateKey);
		openssl_sign($params, $sign, $pkeyid);
		openssl_free_key($pkeyid);
		return base64_encode($sign);
	}

	/**
	 * rsa检查
	 * @param array $data 数据数组
	 * @return boolean 检查通过返回TRUE
	 */
	public function checkRsa(array $params) {
		// 数据准备
		$sign = base64_decode($params['sign']);
		unset($params['sign']);
		$params = urldecode(http_build_query($params));
		$verify = FALSE;
		
		// 对比验证
		$publicKey = file_get_contents($this->getRsaPublicKeyPath());
		if($pkeyid = openssl_get_publickey($publicKey)) {
			$verify = openssl_verify($string, $sign, $pkeyid);
			openssl_free_key($pkeyid);
		}
		return (bool)$verify;
	}

	/**
	 * 抛出异常信息
	 * @param int $code 异常码
	 * @param code $message 异常信息
	 * @return void
	 * @throws \Exception
	 */
	public function throws($code, $message) {
		throw new \Exception($message, $code);
	}
}
