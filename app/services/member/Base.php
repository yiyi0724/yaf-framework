<?php

/**
 * 用户基类
 * @author enychen
 */
namespace services\member;

class Base extends \services\base\Base {

	/**
	 * uid在session的key
	 * @var string
	 */
	const SESSION_UID_KEY = 'user.uid';

	/**
	 * 记住登录cookie的key
	 * @var string
	 */
	const COOKIE_REMEMBER_KEY = '_r';

	/**
	 * 定义一个常量UID，如果uid获取不到则为0，否则为具体的数字
	 * @return void
	 */
	public function initUid() {
		if(!defined('UID')) {
			try {				
				$uid = $this->getSession()->get(self::SESSION_UID_KEY);
				if($remember = $this->getRequest()->getCookie(self::COOKIE_REMEMBER_KEY)) {
					$remember = \security\Encryption::decrypt($remember, MEMBER_REMEMBER_KEY);
					$this->getSession()->set('user.uid', $remember['uid']);
					$uid = $remember['uid'];
				}
				
				define('UID', intval($uid));
			} catch (\Exception $e) {
				exit('403 Forbidden');
			}
		}
	}
}