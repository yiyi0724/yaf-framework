<?php

/**
 * 权限模型
 * @author enychen
 *
 */
namespace Enychen;

class AdminMenuModel extends \Base\AbstractModel {

	/**
	 * 表名
	 * @var string
	 */
	protected $table = 'admin_menu';

	/**
	 * 判断用户是否有访问权限
	 * @param string $controller 控制器名称
	 * @param string $action 方法名称
	 * @return int 返回栏目的id
	 */
	public function hasPermission($controller, $action) {
		return $this->db->field('id')->table($this->table)
			->where(array('controller'=>strtolower($controller), 'action'=>strtolower($action)))
			->select()->fetchColumn();
	}

	/**
	 * 获取用户能操作的权限列表
	 * @param string $rules
	 */
	public function getUserMenus($rules) {

		$where = array('is_column'=>1);
		if($rules != '*') {
			$where['id'] = $rules;
		}

		$menus = $this->db->field('id,name,icon,parent,url,controller,action')->table($this->table)->where($where)->order('parent asc, sort asc')
			->select()->fetchAll();

		$menus = $this->sonTree($menus);
		
		return $menus;
	}
	
	/**
	 * 无限级分类获取子孙树
	 * @param array $menus 初始化栏目
	 * @param int $id 第一层id值
	 * @return array
	 */
	protected function sonTree(array $menus, $id = 0) {
		$tree = array();
		foreach($menus as $item) {
			if($item->parent == $id) {
				$item->isShow = FALSE;
				if(!strcasecmp($item->controller, CONTROLLER) && 
				   !strcasecmp($item->action, ACTION)) {
					$item->isShow = true;
				}

				$tree[] = $item;

				if($children = $this->sonTree($menus, $item->id)) {
					$tree[count($tree)-1]->children = $children;
				}
			}
		}
		
		return $tree;
	}
}