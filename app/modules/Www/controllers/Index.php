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
		$adminModel = new \Admin\UserModel();
		$administrator = $adminModel->getAdminByPW('346745114@qq.com', '7c4a8d09ca3762af61e59520943dc26494f8941b');
		
		echo $administrator->aid;exit;
	}
}