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
		// 初始化登录信息
		$this->initLogin();
		// 初始化侧边栏
		$this->initMenu();
	}

	/**
	 * 初始化登录信息
	 * @return void
	 */
	protected function initLogin() {
		// 初始化常量
		AdminLoginService::initAdminConst();

		// 检查是否已经登录过
		if(!AdminLoginService::chekLogin() && !in_array(CONTROLLER_NAME, self::$noCheck)) {
			AdminLoginService::clear();
			$this->redirect('/login');
		}

		// 进行时间更新
		AdminLoginService::update();
	}

	/**
	 * 初始化左侧栏目
	 * @return void
	 */
	protected function initMenu() {
		if(!in_array(CONTROLLER_NAME, self::$noCheck)) {
			$this->assign('menus', AdminMenuService::getLists());
		}
	}
}