<?php

namespace Base;

use \Yaf\Session;

/**
 * 后台控制基类
 */
abstract class AdminController extends BaseController
{
	public function init()
	{
		// 用户id定义
		define('UID', Session::getInstance()->get('admin.aid'));
				
		// 登录检查
		$this->login();
	}
	
	/**
	 * 登录检查,未登录跳转
	 * @param string $url 跳转地址
	 * @param string $method 跳转方式
	 * @param int|array 跳转code或者post传递参数
	 */
	protected function login($url = "/admin/login", $method = 'get', $data = NULL)
	{
		if(!UID && CONTROLLER != 'Login')
		{
			IS_AJAX ? $this->jsonp($url, 302) : $this->location($url, $method, $data);
		}
	}
}