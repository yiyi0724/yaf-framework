<?php

/**
 * 管理员操作逻辑
 * @author enychen
 */

class AdminLogic {

	/**
	 * 管理员uid常量
	 * @var string
	 */
	const SESSION_UID = 'admin.uid';
	
	/**
	 * 管理员登录时间常量
	 * @var string
	 */
	const SESSION_LOGINTIME = 'admin.logintime';
	
	/**
	 * 管理员登录ip常量
	 * @var string
	 */
	const SESSION_IP = 'admin.ip';
	
	/**
	 * 管理员权限列表
	 * @var string
	 */
	const SESSION_GROUP = 'admin.group';
	
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
	 * 根据uid获取用户的信息
	 * @param int $uid 用户id
	 * @param string $fields 用户字段
	 * @return array
	 */
	public function getAdminstratorByUid($uid, $fields='*') {
		$adminModel = new \Enychen\AdminModel();
		return $adminModel->field($fields)->where(array('uid'=>$uid))->select()->fetch();
	}	

	/**
	 * 从session中删除管理员所有信息
	 * @author chenxb
	 * @return void
	 */
	public function clearAdminSession() {
		$session = $this->getSession();
		$session->del(static::SESSION_UID);
		$session->del(static::SESSION_LOGINTIME);
		$session->del(static::SESSION_IP);
		$session->del(static::SESSION_GROUP);
	}
}