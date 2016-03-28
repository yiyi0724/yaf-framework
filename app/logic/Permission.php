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
	 * 根据用户的权限获取，往上查找
	 * @params array 管理员用户的权限列表
	 */
	public function getMenusByUserPower(array $mids) {
		$menus = array();
		$menuModel = new \Enychen\MenuModel();
		foreach($mids as $mid) {
			while(TRUE) {
				
				if(!$mid || isset($menus[$mid])) {
					break;
				}
				
				$temp = $menuModel->field('id,name,url,icon,parent,sort')->where(['id'=>$mid, 'is_column'=>1])->select()->fetch();				
				$menus[$temp['id']] = $temp;
				$mid = $temp['parent'];
			}
		}
		
		// 进行手动排序
		$menus = $this->iterare($menus);
		usort($menus, function($a, $b) {
			return $a['sort'] == $b['sort'] ? 0 : (($a['sort'] > $b['sort']) ? 1 : -1);
		});
		
		return $menus;
	}
	
	/**
	 * 无限级分类
	 * @param array $menus 初始化栏目
	 * @return array
	 */
	protected function iterare(array $menus) {
		$tree = array();
		foreach($menus as $item){
			if(isset($menus[$item['parent']])){
				$menus[$item['parent']]['children'][] = &$menus[$item['id']];
			}else{
				$tree[] = &$menus[$item['id']];
			}
		}
		return $tree;
	}
}