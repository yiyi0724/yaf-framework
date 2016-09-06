<?php

/**
 * 管理员初始化
 * @author enychen
 */
namespace services\admin;

use \network\IP;
use \services\base\Base as BaseService;
use \admin\UserModel as AdminUserModel;
use \security\Encryption as EncryptionLib;
use \admin\LoginlogModel as AdminLoginLogModel;

class Login extends BaseService {

	/**
	 * 初始化管理员常量
	 * @static
	 * @return void
	 */
	public static function initAdminConst() {
		$session = self::getSession();
		defined('ADMIN_UID') or define('ADMIN_UID', intval($session->get('admin.uid')));
		defined('ADMIN_NAME') or define('ADMIN_NAME', $session->get('admin.name'));
		defined('ADMIN_ISEXPIRE') or define('ADMIN_ISEXPIRE', (time() - $session->get('admin.lasttime') >= 1800));
		defined('ADMIN_IP_MATCH') or define('ADMIN_IP_MATCH', ($session->get('admin.ip') || IP::client() == $session->get('admin.ip')));
	}

	/**
	 * 从cookie中进行登录
	 * @static
	 * @return boolean
	 */
	public static function chekLogin() {
		if(!ADMIN_UID || ADMIN_ISEXPIRE || !ADMIN_IP_MATCH) {
			return FALSE;
		}

		self::getSession()->set('admin.lasttime', time());
		return TRUE;
	}

	/**
	 * 使用账号密码登录
	 * @static
	 * @param string $account 账号
	 * @param string $password 原始密码
	 * @return boolean 账号密码正确返回TRUE
	 */
	public static function useAccountAndPassword($accout, $password) {
		$adminUserModel = new AdminUserModel();
		$admin = $adminUserModel->where("username=:u", $accout)->limit(1)->select()->fetchRow();

		if(!$admin || EncryptionLib::decrypt($admin['password'], PASSWORD_SECRET) != $password) {
			return FALSE;
		}
		
		self::recordSession($admin['id'], $admin['nickname']);
		return TRUE;
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