<?php

class LoginController extends \Base\AdminController
{
	/**
	 * 登录页面
	 */
	public function indexAction()
	{
	}
	
	/**
	 * 进行登录
	 */
	public function loginAction()
	{
		// 获取参数
		$data = $this->validate();
		$this->jsonp('妈的智障', 200);
	}
}