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
	 * 全局控制器初始化信息
	 * @return void
	 */
	public function init() {
		$adminModel = new \Admin\AdminstratorModel();
		
		// 定义管理员uid
		define('AUID', $adminModel->getUidFromSession());
		
		// 登录控制器检查
		if(!in_array(CONTROLLER, $this->noLoginCheck)) {			
			// 登录状态检查
			$this->isLogin();
						
			// ip地址检查&&超时检查
			$ip = \Network\Ip::get();
			$timeout = time() - 900;
			if($adminModel->getIpFromSession() != $ip || ($adminModel->getTimeFromSession() < $timeout)) {
				$adminModel->exitLogin();
				$this->location('/admin/login');
			}
			
			// 更新用户上次使用时间
			$userModel->flushTimeToSession(time());
			
			// 权限控制
			if(!IS_AJAX) {
				$rules = $adminModel->getRules(new \Admin\PermissionModel());
				$menus = $permissionLogic->getMenusByUserPower($rules);
				$this->assign('menus', $menus);
			}
			
			// 权限检查
			$id = $permissionLogic->hasPermission(CONTROLLER, ACTION);
			if(!in_array($id, $rules)) {
				IS_AJAX ? $this->jsonp('您没有操作权限') : $this->notify();
				exit();
			}
		}
	}

	/**
	 * 登录检查,未登录跳转
	 * @param string $url 跳转地址
	 * @param string $method 跳转方式
	 * @param int|array 跳转code或者post传递参数
	 * @return void
	 */
	protected function isLogin($url = "/admin/login", $method = 'get', $data = NULL) {
		!AUID and $this->location($url);
	}
}