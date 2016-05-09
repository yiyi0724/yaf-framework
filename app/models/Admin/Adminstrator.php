<?php

/**
 * 管理员模型
 * @author enychen
 */
namespace Admin;

class AdminstratorModel extends \Base\AbstractModel {
	
	const ADMIN_UID   = 'admin.uid';   // 管理员uid
	const ADMIN_IP    = 'admin.ip';    // 管理员登录ip
	const ADMIN_TIME  = 'admin.time';  // 管理员登录时间
	const ADMIN_RULES = 'admin.rules'; // 管理员权限
	
	/**
	 * 从session中获取管理员uid
	 * @author enychen
	 * @return int 管理员uid
	 */
	public function getUidFromSession() {
		return $this->getSession()->get(static::ADMIN_UID);
	}
	
	/**
	 * 从session中获取管理员uid
	 * @return int 管理员uid
	 */
	public function getIpFromSession() {
		return $this->getSession()->get(static::ADMIN_IP);
	}
	
	/**
	 * 从session中获取管理员uid
	 * @return int 管理员uid
	 */
	public function getTimeFromSession() {
		return $this->getSession()->get(static::ADMIN_TIME);
	}
	
	/**
	 * 更新最新操作的时间
	 * @param int $time 时间戳
	 * @return void
	 */
	public function flushTimeToSession($time) {
		$this->getSession()->set(static::ADMIN_TIME, $time);
	}
	
	public function getRules(\Admin\GroupModel $groupModel) {
		
	}
	
	/**
	 * 管理员退出登录(删除4个admin常量session信息)
	 * @param \Yaf\Session $session session对象
	 * @return void
	 */
	public function exitLogin() {
		$session = $this->getSession();
		$session->del(static::ADMIN_IP);
		$session->del(static::ADMIN_RULES);
		$session->del(static::ADMIN_TIME);
		$session->del(static::ADMIN_UID);
	}
	
	/**
	 * 通过账号密码获取用户
	 * @author enychen
	 * @param string $username 用户名
	 * @param string $password 密码
	 * @return \stdClass|null 找到返回对象，找不到返回NULL
	 */
	public function getAdminByPW($username, $password) {
		return $this->db
			->field('aid, nickname, status, group_id, attach_rules, ag.name')
			->table('admin_users as au')
			->join('admin_group as ag','au.group_id = ag.id')
			->where(array('username'=>$username, 'password'=>sha1($password)))
			->limit(1)
			->select()
			->fetch();
	}
}