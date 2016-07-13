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
	 * 是否记住登录
	 * @var bool
	 */
	protected $isRemember = TRUE;

	/**
	 * 定义一个常量UID，如果uid获取不到则为0，否则为具体的数字
	 * @return void
	 */
	public function initUid() {
		if(!defined('UID')) {
			try {
				$uid = $this->getSession()->get(self::SESSION_UID_KEY);
				// session不存在uid，尝试从记住登录的cookie中获取
				if(!$uid && ($remember = $this->getYafRequest()->getCookie(self::COOKIE_REMEMBER_KEY))) {
					$remember = \security\Encryption::decrypt($remember, MEMBER_REMEMBER_KEY);
					$this->getSession()->set(self::SESSION_UID_KEY, $remember['uid']);
					$uid = $remember['uid'];
				}
				// 常量定义
				define('UID', intval($uid));
			} catch (\Exception $e) {
				header('HTTP/1.1 403 Forbidden');
				exit;
			}
		}
	}

	/**
	 * 设置是否登录
	 * @param bool $isRemember 是否进行登录
	 * @return Login $this 返回当前对象进行连贯操作
	 */
	public function setIsRemember($isRemember) {
		$this->isRemember = (bool)$isRemember;
		return $this;
	}
	
	/**
	 * 获取是否登录
	 * @return string
	 */
	public function getIsRemember() {
		return $this->isRemember;
	}

	/**
	 * 加密密码
	 * @param string $password 待加密的密码
	 * @return string
	 */
	protected function encryptPassword($password) {
		return md5(sha1(md5($password . MEMBER_PASSWORD_KEY, TRUE)));
	}

	/**
	 * 是否记住登录（默认记住一个月）
	 * @return void
	 */
	protected function rememberLogin($time = 2592000) {
		if(!defined('UID')) {
			return;
		}

		$encrypt = \security\Encryption::encrypt(array('uid'=>UID), MEMBER_REMEMBER_KEY);
		setcookie(self::COOKIE_REMEMBER_KEY, $encrypt, time() + $time, '/', NULL, FALSE, TRUE);
	}
}