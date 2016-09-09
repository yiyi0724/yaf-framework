<?php

/**
 * 登录控制器
 * @author enychen
 */

use \admin\LoginService;
use \network\IP as IPLib;
use \image\CaptchaService;
use \security\ChannelService;

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
		$channelKey = sprintf('login.%s', IPLib::client());
		
		// 访问次数控制
		ChannelService::incrChannel($channelKey);

		// 访问路径检查
		if(!$request->isPost() || !$request->isXmlHttpRequest()) {
			$this->throwForbiddenException(1101, '非法访问');
		}

		// 对比验证码
		if(!CaptchaService::compare('login', $request->get('captcha'))) {
			$this->throwNotifyException(1102, '验证码有误');
		}

		// 访问次数检查
		if(ChannelService::getChannelNumber($channelKey) > 7) {
			$this->throwNotifyException(1104, '操作太频繁了，请休息15分钟');
		}

		// 账号密码检查
		$adminInfo = LoginService::accountAndPassword($request->get('account'), $request->get('password'));
		if(!$adminInfo) {
			$this->throwNotifyException(1103, '账号或密码有误');
		}

		// 登录成功后返回
		$this->json(TRUE, '登录成功', 1100);
	}
}