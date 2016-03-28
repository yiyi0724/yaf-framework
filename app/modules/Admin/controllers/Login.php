<?php

/**
 * 登录登出控制器
 * @author enychen
 */
class LoginController extends \Base\AdminController {

	/**
	 * 登录页面
	 */
	public function indexAction() {
		// 是否已经登录
		AUID and $this->location('/admin');
	}

	/**
	 * 进行登录
	 */
	public function loginAction() {
		// 来源地址检查
		if(!IS_POST || !IS_AJAX) {
			$this->jsonp('您无权访问', 412);
		}
		
		// 用户是否已经登录
		if(AUID) {
			$this->location('/admin');
		}
		
		// 参数获取
		$data = $this->validity();
		
		// 验证码检查
		$captChaLogic = new \logic\Captcha();
		if(!$captChaLogic->checkCodeFromSession('login', $data['captcha'])) {
			$this->jsonp('验证码有误', 412);
		}
		
		// 账号密码检查
		$adminLogic = new \logic\Admin();
		$administrator = $adminLogic->getAdministrator($data['username'], $data['password']);
		if(!$administrator) {
			$this->jsonp('账号或密码不正确', 412);
		}
		
		// 写入session，并且跳转
		$session = $adminLogic->getSession();
		$session->set(\logic\Admin::SESSION_UID, $administrator['uid']);
		$session->set(\logic\Admin::SESSION_LOGINTIME, time());
		$session->set(\logic\Admin::SESSION_IP, \Network\Ip::get());
		// 记录用户的权限
		$permissionLogic = new \logic\Permission();
		$rules = $permissionLogic->getRulesByGroupId($administrator['group_id']);
		$session->set(\logic\Admin::SESSION_GROUP, $rules);
		
		$this->location('/admin');
		
		return true;
	}

	/**
	 * 进行登出
	 */
	public function logoutAction() {		
		// 删除session信息
		$adminLogic = new \logic\Admin();
		$adminLogic->clearAdminSession();
		
		// 跳转到登录页面
		$this->location('/admin/login');
	}
}