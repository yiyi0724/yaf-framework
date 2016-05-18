<?php

/**
 * 权限模型
 * @author enychen
 *
 */
namespace Enychen;

class AdminUserModel extends \Base\AbstractModel {

	/**
	 * 表名
	 * @var string
	 */
	protected $table = 'admin_user';
	
	/**
	 * 通过账号密码取管理员的所有信息
	 * @param string　$username 用户名
	 * @param string $password 密码
	 * @return \stdClass
	 */
	public function getAdminByPW($username, $password) {
		$admin = $this->db->field('id,password,nickname,avatar,group_id,status,attach_rules')
			->table($this->table)
			->where(array('username'=>$username))
			->select()
			->fetch();

		return $admin && $admin->password == sha1($password) ? $admin : NULL;
	}
}