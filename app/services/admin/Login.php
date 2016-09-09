<?php

/**
 * 管理员初始化
 * @author enychen
 */
namespace admin;

use \network\IP as IPLib;
use \admin\UserModel as AdminUserModel;
use \security\Encryption as EncryptionLib;
use \admin\LoginlogModel as AdminLoginLogModel;
use \storage\SessionService as StorageStorageSessionService;

class LoginService {

	/**
	 * 初始化管理员常量
	 * @static
	 * @return void
	 */
	public static function initAdminConst() {
		defined('ADMIN_UID') or define('ADMIN_UID', intval(StorageStorageSessionService::get('admin.uid')));
		defined('ADMIN_NAME') or define('ADMIN_NAME',StorageStorageSessionService::get('admin.name'));
		defined('ADMIN_ISEXPIRE') or define('ADMIN_ISEXPIRE', (time() - StorageSessionService::get('admin.lasttime') >= 1800));
		defined('ADMIN_IP_MATCH') or define('ADMIN_IP_MATCH', (StorageSessionService::get('admin.ip') 
			|| IPLib::client() == StorageSessionService::get('admin.ip')));
	}

	/**
	 * 更新参数
	 * @static
	 * @return void
	 */
	public static function update() {
		StorageSessionService::set('admin.lasttime', time());
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
	public static function accountAndPassword($accout, $password) {
		// 数据库检查
		$adminUserModel = new AdminUserModel();
		$admin = $adminUserModel->where("username=:u", $accout)->limit(1)->select()->fetchRow();
		if(!$admin || EncryptionLib::decrypt($admin['password'], PASSWORD_SECRET) != $password) {
			return array();
		}

		// session记录
		StorageSessionService::set('admin.uid', $admin['id']);
		StorageSessionService::set('admin.name', $admin['nickname']);
		StorageSessionService::set('admin.ip', IPLib::client());
		StorageSessionService::set('admin.lasttime', time());

		// 日志记录
		$adminLoginlogModel = new AdminLoginLogModel();
		if($loginlog = $adminLoginlogModel->where('uid=:uid and addtime=:time', $admin['id'], date('Ymd'))->select()->fetchRow()) {
			$adminLoginlogModel->where('id=:id', $loginlog['uid'])->update(array('count'=> $loginlog['count']+1, 'ip'=>IPLib::client()));
		} else {
			$adminLoginlogModel->insert(array('uid'=>$admin['id'], 'addtime'=>date('Ymd'), 'ip'=>IPLib::client(), 'count'=>1));
		}

		return $admin;
	}

	/**
	 * 清空管理员信息
	 * @static
	 * @return Info $this 返回当前对象进行连贯操作
	 */
	public static function clear() {
		StorageSessionService::del('admin.uid');
		StorageSessionService::del('admin.name');
		StorageSessionService::del('admin.ip');
		StorageSessionService::del('admin.lasttime');
	}
}