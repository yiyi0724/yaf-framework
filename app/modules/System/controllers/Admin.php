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
	}

	/**
	 * 获取管理员信息
	 */
	public function listsAction() {
		$request = $this->getRequest();
		$page = $request->get('page');
		
		$adminUserModel = new AdminUserModel();
		$pagitor = $adminUserModel->field('id,username,nickname,mobile,status,addtime')->pagitor($page);
		foreach($pagitor['lists'] as &$list) {
			$list['addtime'] = date('Y-m-d H:i:s', strtotime($list['addtime']));
		}

		$this->json(TRUE, '获取成功', 2001, $pagitor);
	}
}