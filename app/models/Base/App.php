<?php

/**
 * 模型基类，逻辑处理封装
 */
namespace Base;

use \Network\Page;

abstract class AppModel extends BaseModel
{

	/**
	 * 分页获取信息
	 * @param int $page 当前页
	 * @param int $number 每页几条
	 * @param array|string $where where条件
	 * @param string $order 排序条件
	 * @param string $group 分组条件
	 * @param array|string having条件
	 */
	public function getPage($page = 1, $number = 15, $where = NULL, $order = NULL, $group = NULL, $having = NULL)
	{
		// 获取分页数量
		$this->field('COUNT(*)');
		$where and ($this->where($where));
		$group and ($this->group($group));
		$order and ($this->order($order));
		$having and ($this->having($having));
		$count = $this->select()->fetchColumn();
		
		// 获取本页数据
		$this->field('*');
		$this->limit(($page - 1) * $number, $number);
		$lists = $this->select()->fetchAll();
		
		// 输出分页
		$page = Page::showCenter($page, $number, $count);
		$page['lists'] = $lists;
		
		return $page;
	}

	/**
	 * 读取配置信息
	 * @param array $key 键名
	 * @return array|string
	 */
	protected final function getConfig($key)
	{
		$result = Application::app()->getConfig()->get($key);
		return is_string($result) ? $result : $result->toArray();
	}
}