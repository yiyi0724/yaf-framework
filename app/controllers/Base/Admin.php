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
		
		$amdinLogic = new \logic\Admin();
		
		// 定义管理员uid
		define('AUID', $amdinLogic->getUidFromSession());
		
		if(!in_array(CONTROLLER, $this->noLoginCheck)) {
			
			// 登录状态检查
			$this->isLogin();
			
			// 超时检查
			if($amdinLogic->isLoginTimeout()) {
				$amdinLogic->delUinfoFromSession();
				$this->location('/admin/login');
			} else {
				$amdinLogic->setLogintimeToSession();
			}
			
			// 权限控制
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