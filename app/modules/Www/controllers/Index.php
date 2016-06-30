<?php

/**
 * 网站默认控制器
 * @author enychen
 *
 */
class IndexController extends \base\WwwController {

	/**
	 * 网站首页
	 */
	public function indexAction() {
		$test = new \test\UserinfomationModel();
		$result = $test->where('uid=:uid and status=:status', 1, 0)->update()->fetchAll();

		echo '<pre>';
		print_r($result);
		exit;
	}
}