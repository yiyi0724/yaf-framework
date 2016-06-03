<?php

/**
 * 用户Oauth认证
 */
namespace qq\user;

class Oauth extends Base {

	/**
	 * 认证的api地址
	 * @var string
	 */
	private $api = 'https://graph.qq.com/oauth2.0/authorize';
}