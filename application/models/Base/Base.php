<?php

namespace Base;

use \Yaf\Application;
use \Yaf\Config\Ini;
use \Driver\Mysql;
use \Driver\Redis;
use \Html\Page;
use Html\Html;

abstract class BaseModel
{

	/**
	 * 读取配置文件
	 * @var array
	 */
	protected $driver = array();

	/**
	 * 数据库对象
	 * @var \Driver\Mysql
	 */
	protected $mysql = NULL;

	/**
	 * redis对象
	 * @var \Driver\Redis
	 */
	protected $redis = NULL;

	/**
	 * 构造函数,加载配置
	 */
	public function __construct($mysql = 'master', $redis = 'master')
	{
		// 读取配置文件
		$this->driver = new Ini(CONF_PATH . 'driver.ini', Application::app()->environ());
		$this->driver = $this->driver->toArray();
		
		// 获取mysql对象
		$mysql and ($this->mysql = Mysql::getInstance($this->driver['mysql'][$mysql]));
		
		// 获取redis对象
		$redis and ($this->redis = Redis::getInstance($this->driver['redis'][$redis]));
	}

	/**
	 * 读取配置信息
	 * @param unknown $key
	 * @return unknown
	 */
	protected function getConfig($key)
	{
		$result = Application::app()->getConfig()->get($key);
		return is_string($result) ? $result : $result->toArray();
	}

	/**
	 * 分页获取信息
	 * @param array $sql 分页的获取信息
	 * @param array $sql 从其他表补充信息
	 */
	public function getPage($sql)
	{
		// 获取分页数量
		$this->mysql->table($sql['table']);
		$this->mysql->field('COUNT(*)');
		isset($sql['where']) and ($this->mysql->where($sql['where']));
		isset($sql['group']) and ($this->mysql->group($sql['group']));
		isset($sql['order']) and ($this->mysql->order($sql['order']));
		isset($sql['having']) and ($this->mysql->having($sql['having']));
		$count = $this->mysql->select()->fetchColumn();
		
		// 获取本页数据
		$this->mysql->table($sql['table']);
		isset($sql['field']) and ($this->mysql->field($sql['field']));
		isset($sql['where']) and ($this->mysql->where($sql['where']));
		isset($sql['group']) and ($this->mysql->group($sql['group']));
		isset($sql['order']) and ($this->mysql->order($sql['order']));
		isset($sql['having']) and ($this->mysql->having($sql['having']));
		$lists = $this->mysql->limit(($sql['page'] - 1) * $sql['limit'], $sql['limit'])->select()->fetchAll();
		
		// 输出分页
		$page = Page::showCenter($sql['limit'], $count);
		$page['lists'] = $lists;
		
		return $page;
	}
	
	/**
	 * 获取补充的信息
	 * @param unknown $lists
	 * @param unknown $supplement
	 */
	protected function getSupplement($lists, $supplement)
	{
		// 获取补充信息
		if($lists && $supplement)
		{
			foreach($supplement as $table=>$condition)
			{
				$recurse = $condition[0];
				$where = $condition[1];
		
				// 获取补充信息
				$this->mysql->table($table);
				isset($condition[2]) and ($this->mysql->field($condition[2]));
				$this->mysql->where(array($where=>$this->toOneDimensions($lists, $recurse)));
				$supplement = $this->mysql->select()->fetchAll();
			}
		}
		
		return $supplement;
	}

	/**
	 * 二维数组转一维数组
	 */
	protected function toOneDimensions($lists, $key)
	{
		$rescurise = array();
		foreach($lists as $list)
		{
			$rescurise[] = $list[$key];
		}
		
		return array_unique($rescurise);
	}
}

