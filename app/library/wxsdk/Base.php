<?php

/**
 * 微信SDK基类
 * @author enychen
 */
namespace wxsdk;

abstract class Base {

	/**
	 * 获取access_token的接口
	 * @var string
	 */
	const ACCESS_TOKEN_API = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=%s&secret=%s';
	
	/**
	 * 公众号appid
	 * @var string
	 */
	protected $appid = NULL;

	/**
	 * 公众号appSecret
	 * @var string
	 */
	protected $appSecret = NULL;

	/**
	 * 公众号access_token
	 * @var string
	 */
	protected $accessToken = NULL;

	/**
	 * 存储对象
	 * @var storage\Adapter
	 */
	protected $storage = NULL;

	/**
	 * 构造函数
	 * @param string $appid 公众号唯一凭证，不传默认为：WEIXIN_APPID
	 * @param string $appSecret 公众号唯一密钥，不传默认为：WEIXIN_APPSECRET
	 */
	public function __construct($appid = NULL, $appSecret = NULL) {
		// 加载默认配置
		require_once(sprintf("%s/Config.php", __DIR__));
		// 设置必须参数
		$this->setAppid($appid ? : WEIXIN_APPID);
		$this->setAppSecret($appid ? $appSecret : WEIXIN_APPSECRET);
		// 设置保存对象
		$storageClass = sprintf("storage/%s", WEIXIN_STORAGE);
		$this->setStorage(new $storageClass($this->getAppid()));
		// 设置access_token
		$this->getAppSecret() and $this->setAccessToken();
	}

	/**
	 * 设置公众号appiid
	 * @param string $appid 公众号appid
	 * @return Base $this 返回当前对象进行连贯操作
	 */
	protected function setAppid($appid) {
		$this->appid = $appid;
		return $this;
	}

	/**
	 * 获取公众号appid
	 * @return string
	 */
	public function getAppid() {
		return $this->appid;
	}

	/**
	 * 设置公众号appSecret
	 * @param string $appSecret 公众号Secret
	 * @return Base $this 返回当前对象进行连贯操作
	 */
	protected function setAppSecret($appSecret) {
		$this->appSecret = $appSecret;
		return $this;
	}

	/**
	 * 获取公众号appSecret
	 * @return string
	 */
	public function getAppSecret() {
		return $this->appSecret;
	}

	/**
	 * 设置存储对象
	 * @param \weixin\storage\Adapter $storage 存储对象
	 * @return Base $this 返回当前对象进行连贯操作
	 */
	protected function setStorage(storage\Adapter $storage) {
		$this->storage = $storage;
		return $this;
	}

	/**
	 * 获取存储对象
	 * @return \weixin\storage\Adapter
	 */
	public function getStorage() {
		return $this->storage;
	}

	/**
	 * 设置公众号的access_token
	 * @return Base $this 返回当前对象进行连贯操作
	 * @throws WxException
	 */
	protected function setAccessToken() {
		$cacheKey = sprintf("access.token.%s", $this->getAppid());
		// 尝试从本地获取下
		$accessToken = $this->getStorage()->get($cacheKey);
		if(!$accessToken) {
			// 请求access_token接口
			$result = json_decode($this->get(sprintf(self::ACCESS_TOKEN_API, $this->getAppid(), $this->getAppSecret())));
			// 请求如果有误
			if(isset($result->errcode)) {
				$this->throws(100991, "{$result->errmsg}({$result->errcode})");
			}
			// 缓存access_token
			$this->getStorage()->setWithExpire($cacheKey, $result->access_token, 7000);
			// 设置变量
			$this->accessToken = $result->access_token;
		}

		return $this;
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
	protected function xmlEncode(array $params) {
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
	 * @throws WxException
	 */
	protected function xmlDecode($xml) {
		libxml_disable_entity_loader(true);
		$result = @simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
		if(!$result) {
			$this->throws(100992, 'XML数据无法解析');
		}
		return json_decode(json_encode($result), TRUE);
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
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_TIMEOUT, 500);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, TRUE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($ch, CURLOPT_URL, $url);
		$result = curl_exec($ch);
		curl_close($ch);
		return $result;
	}

	/**
	 * 发送post请求
	 * @param string $url  url地址
	 * @param string $params 需要post的字符串数据
	 * @return string
	 */
	protected function post($url, $params) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_TIMEOUT, 500);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, TRUE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
		$result = curl_exec($ch);
		curl_close($ch);
		return $result;
	}

	/**
	 * 微信来源合法性检查
	 * @param string $signature 微信加密签名
	 * @param string $timestamp 时间戳
	 * @param string $nonce	 	随机数
	 * @param string $token		在微信平台设定的token值
	 * @return void
	 * @throws WxException
	 */
	public function checkSignature($signature, $timestamp, $nonce, $token) {
		$signArr = array($token, $timestamp, $nonce);
		sort($signArr, SORT_STRING);
		if(sha1(implode($signArr)) !== $signature) {
			$this->throws(100993, '签名不正确');
		}
	}

	/**
	 * 获取微信推送的消息
	 * @return string xml格式数据
	 */
	public function getPush() {
		return file_get_contents('php://input');
	}

	/**
	 * 抛出异常
	 * @param int $code 错误代码
	 * @param string $message 错误信息
	 * @return void
	 * @throws WxException
	 */
	protected function throws($code, $message) {
		throw new WxException($message, $code);
	}
}