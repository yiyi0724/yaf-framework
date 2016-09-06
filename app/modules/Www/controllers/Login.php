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
			$this->throwForbiddenException(1101, '非法访问');
		}

		// 对比验证码
		if(!CaptchaService::compare('login', $request->get('captcha'))) {
			$this->throwNotifyException(1102, '验证码有误');
		}

		// 账号密码检查
		$adminInfo = AdminLoginService::useAccountAndPassword($request->get('account'), $request->get('password'));
		if(!$adminInfo) {
			$this->throwNotifyException(1103, '账号或密码有误');
		}

		// 日志记录
		AdminLoginService::recordLog($adminInfo['uid'], $adminInfo['nickname']);

		// 登录成功后返回
		$this->json(TRUE, '登录成功', 1100);
	}
}