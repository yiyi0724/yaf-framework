<?php

/**
 * 登录控制器
 * @author enychen
 */

use \services\common\Security as SecurityService;

class LoginController extends \base\BaseController {

	/**
	 * 重置初始化
	 * @return void
	 */
	public function init() {
		// 已经登录
		(defined('ADMIN_UID') && ADMIN_UID) and $this->redirect('/');
	}

	/**
	 * 登录页面
	 * @return void
	 */
	public function indexAction() {
		$securityService = new SecurityService();
		$securityService->set('login', uniqid());
	}

	/**
	 * 进行登录
	 * @return void
	 */
	public function doAction() {
	}
}