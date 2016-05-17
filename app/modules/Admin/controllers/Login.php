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
		ADMIN_UID and $this->redirect('/admin/');
	}

	/**
	 * 进行登录
	 */
	public function loginAction() {
		// 来源地址检查
		(IS_POST && IS_AJAX) or $this->jsonp('您无权访问');

		// 用户是否已经登录
		ADMIN_UID and $this->jsonp('/admin/', 1010);
		
		// 参数获取
		$params = $this->inputFliter();
		$params['ip'] = \Network\IP::get();
				
		// 验证码检查
		if(!\logic\Captcha::check('login', $params['captcha'])) {
			$this->jsonp(array('captcha'=>'验证码有误'), 1020);
		}
		
		// 账号密码检查
		$adminUserModel = new \Enychen\AdminUserModel();
		$admin = $adminUserModel->getAdminByPW($params['username'], $params['password']);
		$admin or $this->jsonp(array('password'=>'账号或密码不正确'), 1020);

		// 不是正常状态
		if($admin->status != 0) {
			$this->jsonp(array('password'=>'帐号状态异常, 请联系管理员'), 1020);
		} 
		
		// 获取用户的权限列表
		$adminGroupModel = new \Enychen\AdminGroupModel();
		$rules = $adminGroupModel->getRulesMergeAttach($admin->group_id, $admin->attach_rules);

		// 写入管理员登录日志
		$adminLoginLogModel = new \Enychen\AdminLoginLogModel();
		$adminLoginLogModel->recordLoginLog($admin->id, $params['ip']);
		
		// 写入session
		$this->getSession()->set('admin.ip', $params['ip']);
		$this->getSession()->set('admin.uid', $admin->id);
		$this->getSession()->set('admin.name', $admin->nickname);
		$this->getSession()->set('admin.lasttime', time());
		$this->getSession()->set('admin.rules', $rules);
		$this->getSession()->set('admin.avatar', $admin->avatar);
		
		// 进行跳转
		$this->jsonp('/admin/', 1010);
	}

	/**
	 * 进行登出
	 */
	public function logoutAction() {
		foreach($this->adminInfo as $value) {
			$this->getSession()->del("admin.{$value}");
		}
		$this->redirect('/admin/login/');
	}
}