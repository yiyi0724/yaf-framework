<?php

/**
 * 网站默认控制器
 * @author enychen
 *
 */
class IndexController extends \Base\WwwController {

	/**
	 * 网站首页
	 */
	public function indexAction() {
		$adminGroupModel = new \Enychen\AdminGroupModel();
		$all = $adminGroupModel->explain();
		
		echo '<pre>';
		print_r($all);
		exit;
		
		exit();
	}
}