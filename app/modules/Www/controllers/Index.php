<?php

/**
 * 网站默认控制器
 * @author enychen
 *
 */
class IndexController extends \Base\WwwController {

	/**
	 * 网站首页
	 */
	public function indexAction() {
		
		// 数据检查
		$params = $this->inputFliter();
	}
}