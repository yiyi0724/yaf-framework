<?php

/**
 * 网站默认控制器
 * @author enychen
 */

use \services\user\Information as InformationService;

class IndexController extends \base\WwwController {

	/**
	 * 网站首页
	 */
	public function indexAction() {
		$this->getRequest();exit;
		
		$userInfoService = new InformationService(1);
		
		echo $userInfoService->getRegIP(),'<hr/>';
		
		exit;
	}
}