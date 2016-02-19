<?php

namespace Base;

/**
 * 后台控制基类
 */

abstract class AdminController extends AppController
{
	/**
	 * 控制器初始化
	 */
	public function init()
	{
		// 初始化用户状态
		$this->member();
		
		// 静态资源常量定义
		$this->resource();
		
		// 默认状态变更
		$this->behavior();
	}
	
	/**
	 * 登录检查,未登录跳转
	 * @param string $url 跳转地址
	 * @param string $method 跳转方式
	 * @param int|array 跳转code或者post传递参数
	 */
	protected function login($url = "/admin/member/login", $method = 'get', $data = NULL)
	{
		if(!UID)
		{
			IS_AJAX ? $this->jsonp($url, 302) : $this->location($url, $method, $data);
		}
	}
}