<?php

class SettingController extends \Base\AdminController {

	/**
	 * 用户首页
	 */
	public function adminAction() {
		// 数据检查
		$params = $this->inputFliter();
		$adminUserModel = new \Enychen\AdminUserModel();
		$pagitor = $adminUserModel->paging($params['p'], 15, array('status'=>array(0, -1)));
		$this->assign('pagitor', $pagitor);
	}
}