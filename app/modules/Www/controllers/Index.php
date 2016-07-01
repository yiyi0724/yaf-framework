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
		
		throw new \traits\ForbiddenException('http://www.baidu.com');
		
		\traits\Request::getInstance();	

		$test = new \test\UserinfomationModel();
		$pagitor = $test->where('uid=:uid and status=:status', 1, 0)->order('uid DESC')->page(1, 15);
		
		$this->assign('page', $pagitor);
	}
}