<?php

/**
 * 登录后的控制器
 * @author enychen
 */
namespace base;

use \services\common\Menu as MenuService;
use \services\admin\Login as AdminLoginService;

abstract class AdminController extends BaseController {

	/**
	 * 不需要检查的控制器
	 * @var array
	 */
	protected $noCheck = array('Login', 'Logout', 'Image');

	/**
	 * 初始化
	 * @return void
	 */
	public function init() {
		if(!in_array(CONTROLLER_NAME, $this->noCheck)) {
			// 初始化管理员信息
			if(!AdminLoginService::fromCookie()) {
				AdminLoginService::clear();
				$this->redirect('/login');
			}
			// 读取侧边栏信息
			$this->assign('menus', MenuService::getLists());
		}
	}
}