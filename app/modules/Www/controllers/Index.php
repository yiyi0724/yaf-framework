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
		$request = $this->getVailRequest();
		$test = new \web\UserinfomationModel();
		$pagitor = $test->order('uid DESC')->pagitor($request->get('p'), 15);
		$this->assign('pagitor', $pagitor);
	}

	/**
	 * 自定义菜单
	 */
	public function menuAction() {
		try {
			$http = new \network\Http();
			$http->setUrl('http://www.test.com');
			$http->setDecode(\network\Http::DECODE_JSON);
			$result = $http->delete(array('username'=>15959375069));
			
			echo '<pre>';
			print_r($result);
			exit;
		} catch (\Exception $e) {
			exit($http->getOriginResult());
		}

	}
}