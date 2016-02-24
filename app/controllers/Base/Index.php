<?php

namespace Base;

/**
 * 前台控制基类
 */

abstract class IndexController extends AppController
{	
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