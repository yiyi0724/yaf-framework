<?php

class LoginController extends \Base\AdminController
{
	/**
	 * 登录页面
	 */
	public function indexAction()
	{
		// 是否已经登录
		AUID and $this->location('/admin');
	}
	
	/**
	 * 进行登录
	 */
	public function loginAction()
	{		
		// 访问权限控制
		if(!IS_POST || !IS_AJAX)
		{
			$this->jsonp('非法访问', 200);
		}
		
		// 是否已经登录
		AUID and $this->location('/admin');
		
		// 获取参数
		$data = $this->validity();
		
		// 获取用户
		$adminLogic = \logic\Admin::getInstance();
		$administrator = $adminLogic->getAdministrator($data['username'], $data['password']);
		
		if(!$administrator)
		{
			$this->jsonp('账号或密码不正确', 200);
		}
		else
		{
			// 写入session，并且跳转
			$adminLogic->setUidToSession($administrator['uid']);
			$this->location('/admin/index');
		}
	}
	
	/**
	 * 进行登出
	 */
	public function logoutAction()
	{
		// 访问权限控制
		if(!IS_GET || !IS_AJAX)
		{
			$this->jsonp('非法访问', 200);
		}
		
		// 删除session信息
		$adminLogic = \logic\Admin::getInstance();
		$adminLogic->delUinfoFromSession();
		
		// 跳转到登录页面
		$this->location('/admin/login');
	}
}