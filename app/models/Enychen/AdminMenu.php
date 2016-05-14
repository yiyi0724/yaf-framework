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
		if($rules == '*') {
			// 超级管理员
			$menus = $this->db->field('id')->table($this->table)->where(array('is_column'=>1))->select()->fetchAll();
			$rules = array();
			foreach($menus as $object) {
				$rules[] = $object->id;
			}
		} else {
			$rules = explode(',', $rules);
		}

		// 获取权限
		$menus = array();
		foreach($rules as $mid) {
			while(TRUE) {
				if(!$mid || isset($menus[$mid])) {
					break;
				}
				$temp = $this->db->field('id,name,url,icon,parent,sort')->table($this->table)
					->where(array('id'=>$mid, 'is_column'=>1))->select()->fetch();
				$menus[$temp->id] = $temp;
				$mid = $temp->parent;
			}
		}
		
		
		// 进行手动排序
		$menus = $this->iterare($menus);
		usort($menus, function ($a, $b) {
			return $a->sort == $b->sort ? 0 : (($a->sort > $b->sort) ? 1 : -1);
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
		foreach($menus as $item) {
			if(isset($menus[$item->parent])) {
				$menus[$item->parent]->children[] = &$menus[$item->id];
			} else {
				$tree[] = &$menus[$item->id];
			}
		}
		
		return $tree;
	}
}