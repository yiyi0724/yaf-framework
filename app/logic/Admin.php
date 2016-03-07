<?php

/**
 * 管理员操作逻辑
 * @author enychen
 */
namespace logic;

class Admin extends Logic
{

	/**
	 * 根据username和password获取一个管理员
	 * @param string $username 管理员账号
	 * @param string $password 管理员密码
	 * @return array 返回包含所有字段的数组，具体字段参考表[eny_admin]，如果不存在则返回空数组
	 */
	public function getAdministrator($username, $password)
	{
		$adminModel = new \Enychen\AdminModel();
		return $adminModel->where(array('username'=>$username, 'password'=>sha1($password)))->select()->fetch();
	}

	/**
	 * 将用户id写入session
	 * @param int $uid 管理员id
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
		$session = $this->getSession();
		$session->del('admin.uid');
		$session->del('admin.logintime');
	}
}