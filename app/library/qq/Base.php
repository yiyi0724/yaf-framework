<?php
/**
 * qqsdk基类
 */
namespace qq;

abstract class Base {

	/**
	 * qq开放平台appid
	 * @var string
	 */
	protected $appid;

	/**
	 * qq开放平台appSecret
	 * @var string
	 */
	protected $appSecret;

	/**
	 * 设置appid
	 * @param string $appid qq开放平台appid
	 * @return void
	 */
	public function setAppid($appid) {
		$this->appid = $appid;
	}

	/**
	 * 获取appid
	 * @return string
	 */
	public function getAppid() {
		return $this->appid;
	}
	
	/**
	 * 设置appSecret
	 * @param string $appid qq开放平台appSecret
	 * @return void
	 */
	public function setAppSecret($appSecret) {
		$this->appSecret = $appSecret;
	}

	/**
	 * 获取appSecret
	 * @return string
	 */
	public function getAppSecret() {
		return $this->appSecret;
	}

	/**
	 * 异常抛出
	 * @param string $code 异常码
	 * @param string $message 异常信息
	 * @throws Exception
	 */
	protected function throws($code, $message) {
		throw new Exception($message, $code);
	}
}