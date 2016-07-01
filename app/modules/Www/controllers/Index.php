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
		throw new \Exception('有错误');
		echo 'cxb';exit;
		$test = new \test\UserinfomationModel();
		$pagitor = $test->where('uid=:uid and status=:status', $request->get('id'), 0)->order('uid DESC')->page(1, 15);
		
		$this->assign('page', $pagitor);
	}
}