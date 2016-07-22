<?php

/**
 * 登录后的控制器
 * @author enychen
 */
namespace base;

use \services\admin\Info as AdminInfoService;

abstract class SystemController extends BaseController {

	/**
	 * 初始化
	 * @return void
	 */
	public function init() {
		// 初始化管理员信息
		try {
			$adminInfoService = new AdminInfoService();
			$adminInfoService->init()->check();
		} catch(RedirectException $e) {
			$this->redirect($e->getMessage());
		}

		// 读取侧边栏信息
		$this->assign('menus', MenuService::getLists());
	}
}