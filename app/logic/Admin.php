<?php

namespace logic;

class Admin extends Logic
{	
	/**
	 * 根据username和password获取一个用户
	 * @param string $username 用户名
	 * @param string $password 密码
	 * @return array 返回包含所有字段的数组，具体字段参考表[eny_admin]
	 */
	public function getAdministrator($username, $password)
	{
		$adminModel = new \Enychen\AdminModel();
		return $adminModel->where(array('username'=>$username, 'password'=>sha1($password)))->select()->fetch();
	}
	
	/**
	 * 将用户id写入session
	 * @param int $uid 用户id
	 */
	public function setUidToSession($uid)
	{
		$session = $this->getSession();
		$session->set('admin.uid', $uid);
		$session->set('admin.logintime', time());
	}
	
	/**
	 * 从session中删除用户id
	 */
	public function delUinfoFromSession()
	{
		$session = Session::getInstance();
		$session->del('admin.uid');
		$session->del('admin.logintime');
	}
}