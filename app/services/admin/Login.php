<?php

/**
 * 管理员初始化
 * @author enychen
 */
namespace admin;

use \network\IP;
use \storage\SessionService;
use \admin\UserModel as AdminUserModel;
use \security\Encryption as EncryptionLib;
use \admin\LoginlogModel as AdminLoginLogModel;

class LoginService {

	/**
	 * 初始化管理员常量
	 * @static
	 * @return void
	 */
	public static function initAdminConst() {
		// 初始化常量
		defined('ADMIN_UID') or define('ADMIN_UID', intval(SessionService::get('admin.uid')));
		defined('ADMIN_NAME') or define('ADMIN_NAME',SessionService::get('admin.name'));
		defined('ADMIN_ISEXPIRE') or define('ADMIN_ISEXPIRE', (time() - SessionService::get('admin.lasttime') >= 1800));
		defined('ADMIN_IP_MATCH') or define('ADMIN_IP_MATCH', (SessionService::get('admin.ip') || IP::client() == SessionService::get('admin.ip')));
		
		// 更新访问时间
		SessionService::set('admin.lasttime', time());
	}

	/**
	 * 从cookie中进行登录
	 * @static
	 * @return boolean
	 */
	public static function chekLogin() {
		return (!ADMIN_UID || ADMIN_ISEXPIRE || !ADMIN_IP_MATCH);
	}

	/**
	 * 使用账号密码登录,登录成功后记录session
	 * @static
	 * @param string $account 账号
	 * @param string $password 原始密码
	 * @return array 账号密码正确返回用户参数，否则返回空数组
	 */
	public static function useAccountAndPassword($accout, $password) {
		// 数据库检查
		$adminUserModel = new AdminUserModel();
		$admin = $adminUserModel->where("username=:u", $accout)->limit(1)->select()->fetchRow();
		if(!$admin || EncryptionLib::decrypt($admin['password'], PASSWORD_SECRET) != $password) {
			return array();
		}

		// session记录
		self::recordSession($admin['id'], $admin['nickname']);

		return $admin;
	}

	/**
	 * 登录后进行session记录
	 * @static
	 * @param int $uid 管理员id
	 * @param string $nickname 管理员昵称
	 * @return void
	 */
	public static function recordSession($uid, $nickname) {
		$session = self::getSession();
		$session->set('admin.uid', $uid);
		$session->set('admin.name', $nickname);
		$session->set('admin.ip', IP::client());
		$session->set('admin.lasttime', time());
	}

	/**
	 * 清空管理员信息
	 * @static
	 * @return Info $this 返回当前对象进行连贯操作
	 */
	public static function clear() {
		$session = self::getSession();
		$session->del('admin.uid');
		$session->del('admin.name');
		$session->del('admin.ip');
		$session->del('admin.lasttime');
	}

	/**
	 * 登录日志记录
	 * @static
	 * @param int $uid 管理员id
	 * @param string $nickname 管理员昵称
	 * @return void
	 */
	public static function recordLog($uid, $nickname) {
		$adminLoginlogModel = new AdminLoginLogModel();
		if($loginlog = $adminLoginlogModel->where('uid=:uid and addtime=:time', $uid, date('Ymd'))->select()->fetchRow()) {
			$adminLoginlogModel->where('id=:id', $loginlog['uid'])->update(array('count'=> $loginlog['count']+1, 'ip'=>IP::client()));
		} else {
			$adminLoginlogModel->insert(array('uid'=>$uid, 'addtime'=>date('Ymd'), 'ip'=>IP::client(), 'count'=>1));
		}
	}
}