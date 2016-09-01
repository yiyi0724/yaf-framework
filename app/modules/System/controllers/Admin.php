<?php

/**
 * 网站默认控制器
 * @author enychen
 */
use \admin\UserModel as AdminUserModel;

class AdminController extends \base\AdminController {

	/**
	 * 网站首页
	 */
	public function indexAction() {
		$request = $this->getRequest();
		$page = $request->get('page');
		
		$adminUserModel = new AdminUserModel();
		$pagitor = $adminUserModel->field('id,username,nickname,status,addtime')->order('addtime DESC')->pagitor($page);
		$this->assign('pagitor', $pagitor);
	}
}