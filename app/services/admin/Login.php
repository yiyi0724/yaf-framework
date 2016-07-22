<?php

/**
 * 管理员初始化
 * @author enychen
 */
namespace services\admin;

use \services\base\Base;
use \network\IP;

class Info extends Base {

	/**
	 * 管理员常量定义
	 * @return void
	 */
	public function init() {
		defined('ADMIN_UID') or define('ADMIN_UID', intval($this->getSession()->get('admin.uid')));
		defined('ADMIN_NAME') or define('ADMIN_NAME', $this->getSession()->get('admin.name'));
		defined('ADMIN_ISEXPIRE') or define('ADMIN_ISEXPIRE', time() - $this->getSession()->get('admin.lasttime'));
		defined('ADMIN_IP_MATCH') or define('ADMIN_IP_MATCH', IP::getClientIP() == $this->getSession()->get('admin.ip'));
	}

	/**
	 * 管理员登录检查
	 * @return void
	 */
	public function check() {
		// 管理员uid检查
		if(!ADMIN_UID) {
			$this->throwRedirectException('\logout');
		}
		
		// 是否过期
		if(ADMIN_ISEXPIRE) {
			$this->getSession()->get('admin.uid', NULL);
			$this->throwRedirectException('\login');
		} else {
			$this->getSession()->set('admin.lasttime', time());
		}
		
		// ip是否不匹配
		if(!ADMIN_IP_MATCH) {
			$this->throwRedirectException('\logout');
		}
	}
}