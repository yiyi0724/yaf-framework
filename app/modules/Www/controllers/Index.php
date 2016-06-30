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
		throw new \Exception('this is a error');
		$test = new \test\UserinfomationModel();
		$test->setGender('yyq');
	}
}