<?php

/**
 * 管理员初始化
 * @author enychen
 */
namespace services\admin;

use \network\IP;
use \services\base\Base;
use \admin\UserModel as AdminUserModel;
use \security\Encryption as EncryptionLib;
use \admin\LoginlogModel as AdminLoginLogModel;

class Login extends Base {

	/**
	 * 从cookie中进行登录
	 * @static
	 * @return boolean
	 */
	public static function fromCookie() {
		$session = self::getSession();
		// 初始化常量
		defined('ADMIN_UID') or define('ADMIN_UID', intval($session->get('admin.uid')));
		defined('ADMIN_NAME') or define('ADMIN_NAME', $session->get('admin.name'));
		defined('ADMIN_ISEXPIRE') or define('ADMIN_ISEXPIRE', (time() - $session->get('admin.lasttime') >= 1800));
		defined('ADMIN_IP_MATCH') or define('ADMIN_IP_MATCH', ($session->get('admin.ip') || IP::client() == $session->get('admin.ip')));

		// 参数检查
		if(!ADMIN_UID || ADMIN_ISEXPIRE || !ADMIN_IP_MATCH) {
			return FALSE;
		}

		// 更新时间
		$session->set('admin.lasttime', time());
	
		return TRUE;
	}

	/**
	 * 使用账号密码登录
	 * @static
	 * @param string $account 账号
	 * @param string $password 原始密码
	 * @return boolean 账号密码正确返回TRUE
	 */
	public static function fromAP($accout, $password) {
		$adminUserModel = new AdminUserModel();
		$admin = $adminUserModel->where("username=:u", $accout)->limit(1)->select()->fetchRow();

		if(!$admin || EncryptionLib::decrypt($admin['password'], PASSWORD_SECRET) != $password) {
			return FALSE;
		}
		
		self::record($admin['id'], $admin['nickname']);
		return TRUE;
	}

	/**
	 * 清空管理员信息
	 * @static
	 * @return Info $this 返回当前对象进行连贯操作
	 */
	public static function clear() {
		$session = self::getSession();
		foreach(array('uid', 'name', 'lasttime', 'ip') as $key) {
			$session->del("admin.{$key}");
		}
	}

	/**
	 * 登录记录
	 * @static
	 * @param int $uid 管理员id
	 * @param string $nickname 管理员昵称
	 * @return void
	 */
	protected static function record($uid, $nickname) {
		// session记录
		$session = self::getSession();
		$session->set('admin.uid', $uid);
		$session->set('admin.name', $nickname);
		$session->set('admin.ip', IP::client());
		$session->set('admin.lasttime', time());

		// 日志记录
		$adminLoginlogModel = new AdminLoginLogModel();
		if($loginlog = $adminLoginlogModel->where('uid=:uid and addtime=:time', $uid, date('Ymd'))->select()->fetchRow()) {
			$adminLoginlogModel->where('id=:id', $loginlog['uid'])->update(array('count'=> $loginlog['count']+1));
		} else {
			$adminLoginlogModel->insert(array('uid'=>$uid, 'addtime'=>date('Ymd'), 'ip'=>IP::client(), 'count'=>1));
		}
	}
}