<?php

/**
 * 登出控制器
 * @author enychen
 */

use \services\admin\Info as AdminInfoService;

class LogoutController extends \base\BaseController {

	/**
	 * 重写初始化
	 * @return void
	 */
	public function init() {		
	}
	
	/**
	 * 登出方法
	 * @return void
	 */
	public function indexAction() {
		$adminInfoService = new AdminInfoService();
		$adminInfoService->clear();
		$this->redirect('\login');
		exit;
	}
}