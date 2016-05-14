<?php

/**
 * 后台权限管理控制
 */
namespace logic;

class Permission extends Logic {

	/**
	 * 根据用户的组id获取用户权限列表
	 * @param int $groupid 组id
	 * @return array
	 */
	public function getRulesByGroupId($groupid) {
		$groupModel = new \Enychen\GroupModel();
		return explode(',', $groupModel->field('rules')->where(array('id'=>$groupid))->select()->fetchColumn());
	}
	
	/**
	 * 检查用户是否拥有权限
	 */
	public function hasPermission($controller, $action) {
		$groupModel = new \Enychen\GroupModel();
		$menuModel = new \EnyChen\MenuModel();
		return $menuModel->field('id')
						 ->where(array('controller'=>strtolower($controller), 'action'=>strtolower($action)))
						 ->select()
						 ->fetchColumn();
	}
	
}