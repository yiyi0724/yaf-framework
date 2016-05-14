<?php

namespace Base;

/**
 * 后台控制基类
 * @enychen
 */
abstract class AdminController extends BaseController {
	
	/**
	 * 不需要进行登录检查的控制器
	 * @var array
	 */
	protected $noLoginCheck = array('Login', 'Captcha');
	
	/**
	 * 用户信息
	 * @var array
	 */
	protected $adminInfo = array('ip', 'uid', 'name', 'lasttime', 'rules', 'avatar');

	/**
	 * 全局控制器初始化信息
	 * @return void
	 */
	public function init() {
		// 定义管理员信息
		foreach($this->adminInfo as $value) {
			$key = 'ADMIN_' . strtoupper($value);
			defined($key) or define($key, $this->getSession()->get("admin.{$value}"));
		}
		
		// 登录控制器检查
		if(!in_array(CONTROLLER, $this->noLoginCheck)) {			
			// 登录状态检查
			$this->isLogin();

			// ip地址检查&&超时检查
			if(ADMIN_IP != \Network\IP::get() || (ADMIN_LASTTIME <= (time() - 900))) {
				$this->redirect('/admin/login/logout');
			}
			
			// 更新用户上次使用时间
			$this->getSession()->set('admin.lasttime', time());
			
			// 权限检查
			$adminMenuModel = new \Enychen\AdminMenuModel();
			$id = $adminMenuModel->hasPermission(CONTROLLER, ACTION);
			if(ADMIN_RULES != '*' && !in_array($id, explode(',', ADMIN_RULES))) {
				IS_AJAX ? $this->jsonp('您没有操作权限') : $this->notify(array('forbidden'=>'您没有操作权限'));
			}
			
			// 获取用户能操作的权限
			if(!IS_AJAX) {
				$menus = $adminMenuModel->getUserMenus(ADMIN_RULES);
				$this->assign('menus', $menus);
			}
		}
	}

	/**
	 * 登录检查,未登录跳转
	 * @param string $url 跳转地址
	 * @return void
	 */
	protected function isLogin($url = "/admin/login") {
		ADMIN_UID or $this->redirect($url, 'get');
	}
}