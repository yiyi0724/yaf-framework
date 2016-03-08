<?php

/**
 * 登录登出控制器
 * @author enychen
 *
 */
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
		IS_POST and IS_AJAX or $this->jsonp('非法访问', 200);
		
		// 已经登录
		AUID and $this->location('/admin');
		
		// 获取参数
		$data = $this->validity();
		
		// 检查验证码信息
		$captChaLogic = new \logic\Captcha();
		if(!$captChaLogic->checkCodeFromSession('login', $data['code']))
		{
			$this->jsonp('验证码有误', 200);
		}
		
		// 获取管理员信息
		$adminLogic = new \logic\Admin();
		$administrator = $adminLogic->getAdministrator($data['username'], $data['password']);
		
		// 用户不存在或者账号密码不正确
		if(!$administrator)
		{
			$this->jsonp('账号或密码不正确', 200);
		}
		
		// 写入session，并且跳转
		$adminLogic->setUidToSession($administrator['uid']);
		$this->location('/admin');
	}
	
	/**
	 * 进行登出
	 */
	public function logoutAction()
	{
		// 访问权限控制
		IS_GET or $this->jsonp('非法访问', 200);
		
		// 删除session信息
		$adminLogic = new \logic\Admin();
		$adminLogic->delUinfoFromSession();
		
		// 跳转到登录页面
		$this->location('/admin/login');
	}
}