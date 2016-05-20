<?php

/**
 * 微信SDK基类
 * @errorCode 
 * 90000 - 微信回调xml数据无法解析
 * 90001 - 微信回调无xml数据传递
 * 90002 - 微信方处理业务失败
 * 90003 - 微信来源不合法
 * 90004 - 微信业务交易失败
 * @author enychen
 */
namespace Weixin;

abstract class Base {

	const AT_KEY = 'weixin.access.token';

	protected $appid = NULL;

	protected $appSecret = NULL;

	protected $accessToken = NULL;

	protected $storage = NULL;

	protected $api = array(
		// 获取微信公众号access_token
		'accessToken'=>'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=%s&secret=%s',
		// 获取微信公众号的jsapi_ticket
		'jsapiTicket'=>'https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token=%s&type=jsapi',
		// 微信统一下单，模式二（需要传递回调地址）
		'unifiedOrder'=>'https://api.mch.weixin.qq.com/pay/unifiedorder',
		// 微信自动帮助生成二维码图片地址
		'qrcode' => 'http://paysdk.weixin.qq.com/example/qrcode.php?data=%s',
		// 引导用户对公众号网站进行授权
		'userCode'=>'https://open.weixin.qq.com/connect/oauth2/authorize?appid=%s&redirect_uri=%s&response_type=code&scope=%s&state=%s#wechat_redirect',
		// 获取用户的access_token
		'userAccessToekn'=>'https://api.weixin.qq.com/sns/oauth2/access_token?appid=%s&secret=%s&code=%s&grant_type=authorization_code',
		// 刷新用户的acess_token
		'userRefreshToken'=>'https://api.weixin.qq.com/sns/oauth2/refresh_token?appid=%s&grant_type=refresh_token&refresh_token=%s',
		// 获取用户的信息
		'userInfo'=>'https://api.weixin.qq.com/sns/userinfo?access_token=%s&openid=%s&lang=%s',
		// 校验用户授权的access_token是否过期
		'userAccessTokenExpire'=>'https://api.weixin.qq.com/sns/auth?access_token=%s&openid=%s',
	);

	/**
	 * 公众号信息保存
	 */
	public function __construct($appid, $appSecret) {
		$this->appid = $appid;
		$this->appSecret = $appSecret;
	}
	
	public function setStorage(\Redis $storage) {
		$this->storage = $storage;
	}
	
	public function getStorage() {
		return $this->storage;
	}
	
	public function getAppid() {
		return $this->appid;
	}
	
	public function getAppSecret() {
		return $this->appSecret;
	}

	/**
	 * 设置公众号的access_token
	 * @param \Redis $storage 存储对象
	 * @return boolean
	 * @throws \Exception
	 */
	public function setAccessToken() {
		if(!$this->getStorage()) {
			throw new \Exception('请先设置storage对象');
		}
		
		// 之前获取的还没有到期
		if($this->accessToken = $this->storage->get(self::AT_KEY)) {
			return TRUE;
		}
		
		// 走微信接口进行请求
		$url = sprintf($this->api['accessToken'], $this->appid, $this->appSecret);
		$result = json_decode($this->get($url));
		if(isset($result->errcode)) {
			throw new \Exception($result->errmsg, $result->errcode);
		}
		
		// 缓存access_token
		$this->storage->set(self::AT_KEY, $result->access_token);
		$this->storage->expire(self::AT_KEY, $result->expires_in);
		
		// 设置变量
		$this->accessToken = $result->access_token;
		
		return TRUE;
	}
	
	public function getAccessToken() {
		return $this->accessToken;
	}

	/**
	 * 将数组转化成xml字符串
	 * @param array $params 要发送的数组
	 * @return string xml字符串
	 */
	protected function XmlEncode($params) {
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
			throw new \Exception('XML数据无法解析', 90000);
		}
		return json_decode(json_encode($result), TRUE);
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
		if($this->useCert) {
			curl_setopt($ch, CURLOPT_SSLCERTTYPE, 'PEM');
			curl_setopt($ch, CURLOPT_SSLCERT, __DIR__ . '/apiclient_cert.pem');
			curl_setopt($ch, CURLOPT_SSLKEYTYPE, 'PEM');
			curl_setopt($ch, CURLOPT_SSLKEY, __DIR__ . '/apiclient_key.pem');
		}

		// 获取结果
		$result = curl_exec($ch);
		curl_close($ch);

		return $result;
	}
}