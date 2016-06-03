<?php

namespace qq\user;

abstract class Base extends \qq\Base {

	/**
	 * api接口信息
	 * @var string
	 */
	protected $api = 'https://graph.qq.com/oauth2.0/authorize';

	/**
	 * 构造函数
	 * @param string $appid qq开放平台appid
	 * @param string $appSecret qq开放平台appSecret
	 * @return void
	 */
	public function __construct($appid, $appSecret) {
		$this->setAppid($appid);
		$this->setAppsecret($appSecret);
	}
}