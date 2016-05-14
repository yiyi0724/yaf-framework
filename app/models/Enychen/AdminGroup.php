<?php

/**
 * 权限模型
 * @author enychen
 *
 */
namespace Enychen;

class AdminGroupModel extends \Base\AbstractModel {

	/**
	 * 表名
	 * @var string
	 */
	protected $table = 'admin_group';
	
	/**
	 * 获取用户的权限列表并且和用户附加权限列表整合
	 * @param int $groupid 组id
	 * @param string $attachRules 权限列表
	 * @return string 权限列表字符串
	 */
	public function getRulesMergeAttach($groupid, $attachRules) {
		$rules = $this->db->field('rules')->table($this->table)
			->where(array('id'=>$groupid))->select()->fetchColumn();
		if($rules != '*') {
			$rules = array_unique(array_merge(explode(',', $rules), explode(',', $attachRules)));
			$rules = implode(',', $rules);
		}
		return $rules;
	}
}