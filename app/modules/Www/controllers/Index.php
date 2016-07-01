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
		$request = $this->getRequest();
		$test = new \test\UserinfomationModel();
		$pagitor = $test->where('uid=:uid and status=:status', $request->get('id'), 0)->order('uid DESC')->page(1, 15);
		
		$this->assign('page', $pagitor);
	}
}