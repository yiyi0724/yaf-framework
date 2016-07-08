<?php

/**
 * 网站默认控制器
 * @author enychen
 */
class IndexController extends \base\MemberController {

	/**
	 * 网站首页
	 */
	public function indexAction() {
		// 测试样例
		$request = $this->getRequest();
		$test = new \test\UserinfomationModel();
		$pagitor = $test->order('uid DESC')->pagitor($request->get('p'), 15);
		$this->assign('pagitor', $pagitor);
	}
}