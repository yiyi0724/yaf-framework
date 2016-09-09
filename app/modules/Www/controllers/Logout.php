<?php

/**
 * 登出控制器
 * @author enychen
 */

use \admin\LoginService;

class LogoutController extends \base\AdminController {

	/**
	 * 登出
	 */
	public function indexAction() {
		LoginService::clear();
		$this->redirect('\login');
	}
}