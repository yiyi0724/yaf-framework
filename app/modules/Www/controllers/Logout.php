<?php

/**
 * 登出控制器
 * @author enychen
 */

use \services\admin\Login as AdminLoginService;

class LogoutController extends \base\AdminController {

	/**
	 * 登出方法
	 * @return void
	 */
	public function indexAction() {
		AdminLoginService::clear();
		$this->redirect('\login');
	}
}