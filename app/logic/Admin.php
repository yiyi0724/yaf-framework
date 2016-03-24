<?php

/**
 * 管理员操作逻辑
 * @author enychen
 */
namespace logic;

class Admin extends Logic {

	const SESSION_UID = 'admin.uid';
	const SESSION_LOGINTIME = 'admin.logintime';
	
	/**
	 * 根据username和password获取一个管理员
	 * @param string $username 管理员账号
	 * @param string $password 管理员密码
	 * @return array 返回包含所有字段的数组，具体字段参考表[eny_admin]，如果不存在则返回空数组
	 */
	public function getAdministrator($username, $password) {
		$adminModel = new \Enychen\AdminModel();
		return $adminModel->where(array('username'=>$username, 'password'=>sha1($password)))->select()->fetch();
	}

	/**
	 * 将管理员id写入session
	 * @param int $uid 管理员id
	 * @return void
	 */
	public function setUidToSession($uid) {
		$this->getSession()->set(static::SESSION_UID, $uid);
	}
	
	/**
	 * 将管理员的登录时间写入session
	 * @return void
	 */
	public function setLogintimeToSession() {
		$this->getSession()->set(static::SESSION_LOGINTIME, time());
	}

	/**
	 * 从session中删除管理员所有信息
	 * @return void
	 */
	public function delUinfoFromSession() {
		$session = $this->getSession();
		$session->del(static::SESSION_UID);
		$session->del(static::SESSION_LOGINTIME);
	}

	/**
	 * 管理员是在一定时间未进行任何操作
	 * @param int $timeout 超时时间
	 * @return boolean
	 */
	public function isLoginTimeout($timeout = 900) {		
		return $this->getSession()->get(static::SESSION_LOGINTIME) < (time() - $timeout);
	}
	
	/**
	 * 从session中获取管理员uid
	 * return string 管理员uid
	 */
	public function getUidFromSession() {
		return $this->getSession()->get(static::SESSION_UID);
	}
}