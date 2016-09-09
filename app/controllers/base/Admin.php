<?php

/**
 * 登录后的控制器
 * @author enychen
 */
namespace base;


use \admin\MenuService as AdminMenuService;
use \admin\LoginService as AdminLoginService;

abstract class AdminController extends BaseController {
	
	/**
	 * 不需要检查的控制器
	 * @var array
	 */
	protected static $noCheck = array('Login', 'Logout', 'Image');

	/**
	 * 初始化
	 * @return void
	 */
	public function init() {
		// 初始化常量
		AdminLoginService::initAdminConst();

		// 检查是否已经登录过
		if(!AdminLoginService::chekLogin() && !in_array(CONTROLLER_NAME, self::$noCheck)) {
			AdminLoginService::clear();
			$this->redirect('/login');
		}

		// 读取侧边栏信息
		$this->assign('menus', AdminMenuService::getLists());
	}
}