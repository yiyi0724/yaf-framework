<?php

/**
 * 微信支付SDK基类
 * @author enychen
 */
namespace weixin\pay;

abstract class Base extends \weixin\Base {

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
	 * @return void
	 */
	public function setKey($key) {
		$this->key = $key;
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
	 * @return void
	 */
	public function setMchid($mchid) {
		$this->mchid = $mchid;
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
	 * @return void
	 */
	public function setProxyHost($proxyHost) {
		$this->proxyHost = $proxyHost;
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
	 * @return void
	 */
	public function setProxyPort($proxyPort) {
		$this->proxyPort = $proxyPort;
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
	 * @return void
	 */
	public function setIsUseCert($isUseCert) {
		$this->isUseCert = $isUseCert;
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
	protected function get($url) {
		$ch = curl_init();
	
		// 初始化设置
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($curl, CURLOPT_TIMEOUT, 500);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, TRUE);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($curl, CURLOPT_URL, $url);
	
		// 如果有配置代理这里就设置代理
		if($this->proxyHost && $this->proxyPort) {
			curl_setopt($ch, CURLOPT_PROXY, $this->proxyHost);
			curl_setopt($ch, CURLOPT_PROXYPORT, $this->proxyPort);
		}
	
		// 设置证书, cert 与 key 分别属于两个.pem文件
		if($this->useCert) {
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
	 * 发送post请求
	 * @param string $url  url地址
	 * @param string $params 需要post的xml字符串数据
	 * @return string
	 */
	protected function post($url, $params) {
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
		if($this->proxyHost && $this->proxyPort) {
			curl_setopt($ch, CURLOPT_PROXY, $this->proxyHost);
			curl_setopt($ch, CURLOPT_PROXYPORT, $this->proxyPort);
		}
	
		// 设置证书, cert 与 key 分别属于两个.pem文件
		if($this->isUseCert) {
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
	protected function verify($result) {
		// 数据来源检查
		if(!$result) {
			$this->throws(1090, '来源非法');
		}

		// 把数据转成xml
		$result = $this->xmlDecode($result);

		// 签名检查
		if($this->sign($result) !== $result->sign) {
			$this->throws(1091, '签名不正确');
		}

		// 微信方通信是否成功
		if($result->return_code != 'SUCCESS') {
			$this->throws(1092, $data->return_msg);
		}

		// 微信业务处理是否失败
		if(isset($result->result_code) && $result->result_code == 'FAIL') {
			$this->throws(1093, $result->err_code_des);
		}

		return $result;
	}
}