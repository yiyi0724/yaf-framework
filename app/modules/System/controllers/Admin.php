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
		$page = $request->get('page', 1);
		
		$adminUserModel = new AdminUserModel();
		$pagitor = $adminUserModel->field('id,username,nickname,status,addtime')->order('addtime DESC')->pagitor($page);
		foreach($pagitor['lists'] as &$list) {
			$list['addtime'] = date('Y-m-d H:i:s', strtotime($list['addtime']));
		}
		$this->assign('pagitor', $pagitor);
	}
}