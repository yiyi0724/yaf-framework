<?php

namespace Base;

use \Yaf\Session;

/**
 * 后台控制基类
 */
abstract class AdminController extends BaseController
{

	/**
	 * 全局控制器初始化信息
	 */
	public function init()
	{
		// 父类init方法
		parent::init();
		
		// 不是登录控制器进行检查
		CONTROLLER != 'Login' and $this->login() and $this->timeout();
	}

	/**
	 * 登录检查,未登录跳转
	 * @param string $url 跳转地址
	 * @param string $method 跳转方式
	 * @param int|array 跳转code或者post传递参数
	 */
	protected function login($url = "/admin/login", $method = 'get', $data = NULL)
	{
		if(!AUID)
		{
			IS_AJAX ? $this->jsonp($url, 302) : $this->location($url, $method, $data);
		}
	}

	/**
	 * 15分钟未进行任何操作，则重新登录
	 */
	protected function timeout()
	{
		$session = Session::getInstance();
		$logintime = $session->get('admin.logintime');
		if($logintime <= (time() - 900))
		{
			// 登录超时
			$session->del('admin.uid');
			IS_AJAX ? $this->jsonp('/admin/login', 302) : $this->location('/admin/login');
		}
		else
		{
			// 更新时间
			$session->set('admin.logintime', time());
		}
	}
}