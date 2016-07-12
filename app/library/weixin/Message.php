<?php

/**
 * 微信消息管理SDK
 * @author enychen
 */
namespace weixin;

class Message extends Base {

	/**
	 * 微信token值
	 * @var string
	 */
	protected $token = NULL;

	/**
	 * 构造函数
	 * @param string $appid 公众号appid
	 * @param string $appSecret 公众号appSecret
	 * @param \storage\Adapter $storage 存储对象
	 * @param string $token 微信token值
	 */
	public function __construct($appid, $appSecret, \storage\Adapter $storage, $token) {
		$this->setAppid($appid);
		$this->setAppSecret($appSecret);
		$this->setStorage($storage);
		$this->setAccessToken();
		$this->setToken($token);
	}

	/**
	 * 设置token值
	 * @param string $token 微信token值
	 * @return Message $this 返回当前对象进行连贯操作
	 */
	public function setToken($token) {
		$this->token = $token;
		return $this;
	}

	/**
	 * 获取token值
	 * @return string
	 */
	public function getToken() {
		return $this->token;
	}

	/**
	 * 检查来源并返回微信推送的消息数组
	 * @return array
	 */
	public function getMessage() {
		// 来源合法性检查
		if(!$this->checkSignature($_REQUEST['signature'], $_REQUEST['timestamp'], $_REQUEST['nonce'], $this->getToken())) {
			$this->throws(1, '微信消息来源非法');
		}

		// 获取解析后的数据
		return $this->xmlDecode($this->getPush());
	}
}