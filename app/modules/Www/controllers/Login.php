<?php

/**
 * 登录控制器
 * @author enychen
 */

use \services\common\Captcha as CaptchaService;
use \services\admin\Login as AdminLoginService;
use \services\common\Security as SecurityService;

class LoginController extends \base\AdminController {

	/**
	 * 登录页面
	 */
	public function indexAction() {
		// 已经登录
		ADMIN_UID and $this->redirect('/');
	}

	/**
	 * 进行登录
	 */
	public function apiAction() {
		$request = $this->getRequest();

		// 访问路径检查
		if(!$request->isPost() || !$request->isXmlHttpRequest()) {
			$this->json(FALSE, '非法访问', 1101);
		}

		// 保存验证码
		if(!CaptchaService::compare('login', $request->get('captcha'))) {
			$this->json(FALSE, '验证码有误', 1102);
		}

		// 账号密码检查
		if(!AdminLoginService::fromAP($request->get('account'), $request->get('password'))) {
			$this->json(FALSE, '账号或密码有误', 1103);
		}
		
		$this->json(TRUE, '登录成功', 1100);
	}
}