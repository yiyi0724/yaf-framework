<?php

/**
 * 支付宝SDK基类
 * @author enychen
 */
namespace alibaba\pay;

abstract class Base extends \alibaba\Base {

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
	 * _input_charset 字符编码
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
	 * 请求地址api
	 * @var string
	 */
	protected $api = 'https://mapi.alipay.com/gateway.do?_input_charset=%s';

	/**
	 * 构造函数
	 * @param string $partner 合作者id
	 * @param string $key	  合作者密钥
	 * @return void
	 */
	public function __construct($partner, $key) {
		$this->setPartner($partner);
		$this->setKey($key);
	}

	/**
	 * 设置合作者id
	 * @param string $partner 合作者id
	 * @return void
	 */
	public function setPartner($partner) {
		$this->partner = $partner;
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
	 * @return void
	 */
	public function setKey($key) {
		$this->key = $key;
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
	 * @return void
	 */
	public function setInputCharset($inputCharset) {
		$this->inputCharset = $inputCharset;
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
	 * @return void
	 */
	public function setSignType($signType) {
		$this->signType = $signType;
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
	 * @return void
	 */
	public function setNotifyUrl($notifyUrl) {
		$this->notifyUrl = $notifyUrl;
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
	 * @return void
	 */
	public function setReturnUrl($returnUrl) {
		$this->returnUrl = $returnUrl;
	}
	
	/**
	 * 设置页面跳转同步通知页面路径
	 * @return string
	 */
	public function getReturnUrl() {
		return $this->returnUrl;
	}

	/**
	 * 设置请求的api地址
	 * @param string $api
	 * @return void
	 */
	protected function setApi($api) {
		$this->api = $api;
	}

	/**
	 * 获取api地址
	 * @return string
	 */
	public function getApi() {
		return $this->api;
	}

	/**
	 * 过滤空值|sign|sign_type
	 * @param array $params 参数列表
	 * @return array 过滤后的数组
	 */
	protected function filterParams($params){
		foreach($params as $key=>$value){
			if(in_array($key, array('sign', 'sign_type')) || !$value){
				unset($params[$key]);
			}
		}

		ksort($params);
		return $params;
	}

	/**
	 * 数据进行签名
	 * @param array $params 参数列表
	 * @return string 签名字符串
	 */
	protected function sign($params){
		switch($this->signType){
			case "MD5":
				return md5(urldecode(http_build_query($params)) . $this->key);
				break;
			default:
				return NULL;
		}
	}
}
