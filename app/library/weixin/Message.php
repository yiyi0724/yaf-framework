<?php

/**
 * 微信消息管理SDK
 * @author enychen
 */
namespace weixin;

class Message extends Base {

	/**
	 * 构造函数
	 * @param string $appid 公众号appid
	 * @param string $appSecret 公众号appSecret
	 * @param \storage\Adapter $storage 存储对象
	 */
	public function __construct($appid, $appSecret, \storage\Adapter $storage) {
		$this->setAppid($appid);
		$this->setAppSecret($appSecret);
		$this->setStorage($storage);
		$this->setAccessToken();
	}

	/**
	 * 获取消息对象
	 */
	public function getMessage() {
	}
}