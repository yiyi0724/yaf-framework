<?php

/**
 * 左侧栏目表
 */
namespace services\menu;

class Lists extends \services\base\Base {

	public function getLists() {
		$adminMenuModel = new \web\AdminmenuModel();
		$menus = $adminMenuModel->field('id,name,icon,parent,module,controller,action,url')
			->where('is_column=:is', 1)->order('sort ASC')->select()->fetchAll();
		$menus = $this->recursion($menus);
		return $menus;
	}

	/**
	 * 递归获取子孙树
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