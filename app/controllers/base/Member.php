<?php

namespace base;

/**
 * 用户控制器
 */
abstract class MemberController extends BaseController {

	/**
	 * 初始化信息
	 * @see \base\BaseController::init()
	 */
	public function init() {
		parent::init();

		// 已经登录却还要登录和注册
		if((in_array(CONTROLLER_NAME, array('Login', 'Reg')) && UID)) {
			$this->redirect('/member/');
		}
		// 没有登录却想访问用户中心
		if(!UID) {
			$this->redirect('/member/login/', 'get');
		}
	}
}