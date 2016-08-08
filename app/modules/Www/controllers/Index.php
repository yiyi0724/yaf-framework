<?php

/**
 * 网站默认控制器
 * @author enychen
 */
class IndexController extends \base\WwwController {

	/**
	 * 网站首页
	 */
	public function indexAction() {
		
		$userInfomationModel = new \example\ExampleModel();
		$userLauthModel = new \example\ExampleModel();
		$lists = $userInfomationModel->select()->fetchAll();		
		echo '<pre>';
		print_r($lists);
		exit;
	}
}