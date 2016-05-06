<?php

namespace Admin;

class UserModel extends \Base\AbstractModel {
	
	/**
	 * 通过账号密码获取用户
	 * @param string $username 用户名
	 * @param string $password 密码
	 * @return array|null 找到返回一维数组，找不到返回NULL
	 */
	public function getAdminByPW($id) {
		define('DEBUG_SQL', TRUE);
		return $this->db
		->field('aid, nickname, status, group_id, attach_rules, ag.name')
		->table('admin_users as au')
		->join('admin_group as ag','au.group_id = ag.id')
		->where(array('id'=>array(1,3)))
		->limit(1)
		->select()
		->fetch();
	}
}