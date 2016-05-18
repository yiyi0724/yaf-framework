<?php

/**
 * 权限模型
 * @author enychen
 *
 */
namespace Enychen;

class AdminLoginLogModel extends \Base\AbstractModel {

	/**
	 * 表名
	 * @var string
	 */
	protected $table = 'admin_login_log';

	/**
	 * 记录用户的登录日志
	 * @param int $uid 管理员uid
	 * @param int $ip 管理员登录ip
	 * @return bool
	 */
	public function recordLoginLog($uid, $ip) {
		try {
			$where = array('uid'=>$uid, 'login_time'=>date('Ymd'), 'login_ip'=>$ip);
			$log = $this->db->field('id,login_count')->table($this->table)->where($where)->select()->fetch();
			if($log) {
				$set['login_count'] = $log->login_count + 1;
				$this->db->table($this->table)->update($set, $where);
			} else {
				$insert['uid'] = $uid;
				$insert['login_time'] = date('Ymd');
				$insert['login_ip'] = $ip;
				$insert['login_count'] = 1;
				$this->db->table($this->table)->insert($insert);
			}
		} catch(\Exception $e) {
			return FALSE;
		}

		return TRUE;
	}
}