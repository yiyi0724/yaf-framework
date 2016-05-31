<?php

/**
 * 微信SDK基类
 * @author enychen
 */
namespace weixin;

abstract class Base {

	/**
	 * access_token缓存键
	 * @var string
	 */
	const ACCESS_TOKEN_KEY = 'weixin.access.token.%s';

	/**
	 * appid信息
	 * @var string
	 */
	protected $appid = NULL;

	/**
	 * appSecret信息
	 * @var string
	 */
	protected $appSecret = NULL;

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
	 * 公众号access_token信息
	 * @var string
	 */
	protected $accessToken = NULL;

	/**
	 * 存储对象
	 * @var \storage\Adapter
	 */
	protected $storage = NULL;

	/**
	 * 设置公众号id
	 * @param string $appid 公众号appid
	 * @return void
	 */
	protected function setAppid($appid) {
		$this->appid = $appid;
	}

	/**
	 * 设置公众号Secret
	 * @param string $appSecret 公众号Secret
	 * @return void
	 */
	protected function setAppSecret($appSecret) {
		$this->appSecret = $appSecret;
	}

	/**
	 * 设置存储对象
	 * @param \storage\Adapter $storage 存储对象
	 * @return void
	 */
	protected function setStorage($storage) {
		$this->storage = $storage;
	}

	/**
	 * 设置商户密钥
	 * @param string $key 密钥串
	 * @return void
	 */
	protected function setKey($key) {
		$this->key = $key;
	}

	/**
	 * 设置商户id
	 * @param string $mchid 商户id
	 * @return void
	 */
	protected function setMchid($mchid) {
		$this->mchid = $mchid;
	}

	/**
	 * 设置代理服务器信息
	 * @param string $proxyHost 理服务器ip地址
	 * @return void
	 */
	protected function setProxyHost($proxyHost) {
		$this->proxyHost = $proxyHost;
	}

	/**
	 * 设置代理服务器端口信息
	 * @param string $proxyPort 理服务器端口地址
	 * @return void
	 */
	protected function setProxyPort($proxyPort) {
		$this->proxyPort = $proxyPort;
	}

	/**
	 * 是否使用ssl证书
	 * @param bool $useCert 是否使用ssl证书
	 * @return void
	 */
	protected function setIsUseCert($isUseCert) {
		$this->isUseCert = $isUseCert;
	}

	/**
	 * 设置公众号的access_token
	 * @return boolean
	 * @throws \weixin\Exception
	 */
	protected function setAccessToken() {
		// 缓存appid的键
		$cacheKey = sprintf(self::ACCESS_TOKEN_KEY, $this->appid);

		// 之前获取的还没有到期
		if($this->accessToken = $this->storage->get($cacheKey)) {
			return TRUE;
		}
		
		// 走微信接口进行请求
		$url = sprintf(API::GET_ACCESS_TOKEN, $this->appid, $this->appSecret);
		$result = json_decode($this->get($url));
		if(isset($result->errcode)) {
			throw new Exception($result->errmsg, $result->errcode);
		}
		
		// 缓存access_token
		$this->storage->set($cacheKey, $result->access_token, $result->expires_in);
		
		// 设置变量
		$this->accessToken = $result->access_token;
		
		return TRUE;
	}

	/**
	 * 获取access_token信息
	 * @return string
	 */
	public function getAccessToken() {
		return $this->accessToken;
	}

	/**
	 * 将数组转化成xml字符串
	 * @param array $params 要发送的数组
	 * @return string xml字符串
	 */
	protected function XmlEncode(array $params) {
		$xml = "<xml>";
		foreach($params as $key=>$value) {
			$xml .= is_numeric($value) ? "<{$key}>{$value}</{$key}>" : "<{$key}><![CDATA[{$value}]]></{$key}>";
		}
		$xml .= "</xml>";
		
		return $xml;
	}

	/**
	 * 将xml字符串转成数组
	 * @param string $xml xml字符串
	 * @return array 解析后的数组
	 * @throw \Exception
	 */
	protected function xmlDecode($xml) {
		libxml_disable_entity_loader(true);
		$result = @simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
		if(!$result) {
			throw new Exception('XML数据无法解析', 1990);
		}
		return json_decode(json_encode($result), TRUE);
	}

	/**
	 * 生成sign签名
	 * @param array $params 原始数据
	 * @return string
	 */
	protected function sign($params) {
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
		$sign .= "&key={$this->key}";
		// 签名步骤三：MD5加密
		$sign = md5($sign);
		// 签名步骤四：所有字符转为大写
		$sign = strtoupper($sign);
		// 返回签名
		return $sign;
	}

	/**
	 * 获取随机字符串
	 * @return string
	 */
	protected function strShuffle() {
		$chars = 'abcdefghijklmnopqrstuvwxyz0123456789';
		$str = '';
		for($i = 0; $i < 32; $i++) {
			$str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
		}
		return $str;
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
	 * 微信来源合法性检查
	 * @param string $signature 微信加密签名
	 * @param string $timestamp 时间戳
	 * @param string $nonce	 	随机数
	 * @return boolean
	 */
	public function checkSignature($signature, $timestamp, $nonce, $token) {
		$signArr = array($token, $timestamp, $nonce);
		sort($signArr, SORT_STRING);
		$signArr = sha1(implode($signArr));
		return $signArr === $signature;
	}

	/**
	 * 获取参数
	 */
	public function getParams($signature, $timestamp, $nonce, $token) {
		if(!$this->checkSignature($signature, $timestamp, $nonce, $token)) {
			throw new \weixin\Exception('出错了');	
		}

		return $this->xmlDecode(file_get_contents('php://input'));
	}
}