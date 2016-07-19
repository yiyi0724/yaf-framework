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
	 * 
	 */
	public function setcookieAction() {
		\network\Header::p3p();
		$cookieLib = new \network\Cookie();
		$cookieLib->setExpire(3600);
		$cookieLib->setName('_test');
		$cookieLib->setValue('1');
		$cookieLib->setDomain('.enychen.com');
		$cookieLib->add();
		exit;
	}

	public function getcookieAction() {
		$this->debug($_COOKIE);
	}
}