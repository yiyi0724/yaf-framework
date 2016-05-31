<?php

class AdminController extends \base\AdminController {

	/**
	 * 管理员列表
	 */
	public function indexAction() {
		// 数据检查
		$params = $this->inputFliter();
		$adminUserModel = new \Enychen\AdminUserModel();
		$pagitor = $adminUserModel->paging($params['p'], 15, array('status'=>array(0, -1)));
		$this->assign('pagitor', $pagitor);
	}

	public function groupAction() {

	}
}