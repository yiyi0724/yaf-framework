<?php

/**
 * 微信支付SDK基类
 * @author enychen
 */
namespace wxsdk\pay;

class Base extends \wxsdk\Base {

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
	protected $useCert = FALSE;

	/**
	 * 参数数组
	 * @var array
	 */
	protected $info = array();

	/**
	 * 构造函数
	 * @param string $appid 公众号appid，不传递默认使用配置文件信息
	 * @param string $mchid 商户id，不传递默认使用配置文件信息
	 * @param string $key 商户密钥，不传递默认使用配置文件信息
	 */
	public function __construct($appid = NULL, $mchid = NULL, $key = NULL) {
		parent::__construct($appid);
		$this->setMchid($mchid ? : WEIXIN_PAY_MCH_ID);
		$this->setKey($key ? : WEIXIN_PAY_KEY);
	}

	/**
	 * 设置商户密钥
	 * @param string $key 密钥串
	 * @return Base $this 返回当前对象进行连贯操作
	 */
	protected function setKey($key) {
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
	 * @return Base $this 返回当前对象进行连贯操作
	 */
	protected function setMchid($mchid) {
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
	 * @param string $proxyHost 代理服务器ip地址
	 * @return Base $this 返回当前对象进行连贯操作
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
	 * @return Base $this 返回当前对象进行连贯操作
	 */
	public function setProxyPort($proxyPort) {
		$this->proxyPort = $proxyPort;
		return $this;
	}

	/**
	 * 代理服务器端口信息
	 * @return int
	 */
	public function getProxyPort() {
		return $this->proxyPort;
	}

	/**
	 * 设置使用ssl证书
	 * @return Base $this 返回当前对象进行连贯操作
	 */
	public function setUseCert() {
		$this->useCert = TRUE;
		return $this;
	}

	/**
	 * 获取是否使用ssl证书
	 * @return boolean
	 */
	public function getUseCert() {
		return $this->useCert;
	}

	/**
	 * 生成sign签名
	 * @param array $params 原始数据
	 * @return string
	 */
	protected function sign(array $params) {
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
		if($this->getProxyHost() && $this->getProxyPort()) {
			curl_setopt($ch, CURLOPT_PROXY, $this->getProxyHost());
			curl_setopt($ch, CURLOPT_PROXYPORT, $this->getProxyPort());
		}

		// 设置证书, cert 与 key 分别属于两个.pem文件
		if($this->getUseCert()) {
			curl_setopt($ch, CURLOPT_SSLCERTTYPE, 'PEM');
			curl_setopt($ch, CURLOPT_SSLCERT, sprintf("%s/apiclient_cert.pem", __DIR__));
			curl_setopt($ch, CURLOPT_SSLKEYTYPE, 'PEM');
			curl_setopt($ch, CURLOPT_SSLKEY, sprintf("%s/apiclient_key.pem", __DIR__));
		}

		$result = curl_exec($curl);
		curl_close($curl);

		return $result;
	}

	/**
	 * 发送post请求
	 * @param string $url url地址
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
		if($this->getProxyHost() && $this->getProxyPort()) {
			curl_setopt($ch, CURLOPT_PROXY, $this->proxyHost);
			curl_setopt($ch, CURLOPT_PROXYPORT, $this->proxyPort);
		}

		// 设置证书, cert 与 key 分别属于两个.pem文件
		if($this->getUseCert()) {
			curl_setopt($ch, CURLOPT_SSLCERTTYPE, 'PEM');
			curl_setopt($ch, CURLOPT_SSLCERT, sprintf("%s/apiclient_cert.pem", __DIR__));
			curl_setopt($ch, CURLOPT_SSLKEYTYPE, 'PEM');
			curl_setopt($ch, CURLOPT_SSLKEY, sprintf("%s/apiclient_key.pem", __DIR__));
		}

		// 获取结果
		$result = curl_exec($ch);
		curl_close($ch);

		return $result;
	}

	/**
	 * 检查curl返回的结果是否合法
	 * @param string $result xml字符串数据
	 * @return void
	 * @throws \wxsdk\WxException
	 */
	protected function checkSignature($result) {
		// 数据来源检查
		if(!$result) {
			$this->throws(100011, '来源非法');
		}

		// 把数据转成xml
		$result = $this->xmlDecode($result);

		// 签名检查
		if($this->sign($result) !== $result['sign']) {
			$this->throws(100012, '签名不正确');
		}

		// 微信方通信是否成功
		if($result['return_code'] != 'SUCCESS') {
			$this->throws(100013, $data['return_msg']);
		}

		// 微信业务处理是否失败
		if(isset($result['result_code']) && $result['result_code'] == 'FAIL') {
			$this->throws(100014, $result['err_code_des']);
		}
	}

	/**
	 * 封装get方法，防止notice报错
	 * @param string $key 键名
	 * @param string $default　默认值
	 * @return string|number|null
	 */
	protected function getInfo($key, $default = NULL) {
		return isset($this->info[$key]) ? $this->info[$key] : $default;
	}

	/**
	 * 获取所有设置的参数信息
	 * @return array
	 */
	protected function toArray() {
		return $this->info;
	}
}