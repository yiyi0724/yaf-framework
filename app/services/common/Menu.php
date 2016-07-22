<?php

/**
 * 左侧栏目逻辑
 * @author enychen
 */
namespace services\common;

class Menu extends \services\base\Base {

	/**
	 * 获取左侧栏目列表，并以子孙树排列
	 * @return array
	 */
	public function getLists() {
		$adminMenuModel = new \web\AdminmenuModel();
		$menus = $adminMenuModel->field('id,name,icon,parent,module,controller,action,url')
			->where('is_column=:is', 1)->order('sort ASC')->select()->fetchAll();
		$menus = $this->recursion($menus);
		return $menus;
	}

	/**
	 * 递归获取子孙树
	 * @param array $menus 列表
	 * @param array $id 选中的栏目列表
	 * @return array
	 */
	private function recursion(array $menus, $id = 0) {
		$tree = array();
		foreach($menus as $item) {
			if($item['parent'] == $id) {
				$tree[] = $item;
				if($children = $this->recursion($menus, $item['id'])) {
					$tree[count($tree)-1]['children'] = $children;
				}
			}
		}
		
		return $tree;
	}
}