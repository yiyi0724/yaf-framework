<?php

/**
 * 网站登出控制器
 * @author enychen
 */
class LogoutController extends InitController {

	/**
	 * 默认控制器
	 */
	public function indexAction() {
        $this->redirect('/login/');
    }
}