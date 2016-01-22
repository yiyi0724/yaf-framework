<?php

namespace Traits;

use \Yaf\Application;
use \Yaf\Config\Ini;
use \Driver\Mysql;
use \Driver\Redis;
use \Html\Page;

abstract class Model
{

	/**
	 * 表名
	 * @var string
	 */
	protected $table;
	
	/**
	 * 附加的查询条件
	 * @var array
	 */
	protected $sql = array(
		'field' => '*',
		'where' => NULL,
		'group' => NULL,
		'having' => NULL,
		'order' => NULL,
		'limit' => NULL,
		'prepare' => NULL,
		'keys' => NULL,
		'values' => array()
	);
	
	/**
	 * 迭代器
	 * @var int
	 */
	protected $interval = 0;

	/**
	 * 读取配置文件
	 * @var array
	 */
	protected $driver = array();

	/**
	 * 构造函数,加载配置
	 */
	protected function __construct()
	{
		// 读取配置文件
		$this->driver = new Ini(CONF_PATH . 'driver.ini', Application::app()->environ());
		$this->driver = $this->driver->toArray();
	}

	/**
	 * 获取mysql
	 * @param string $key
	 * @return \Driver\Mysql
	 */
	protected function mysql($key = 'master')
	{
		return Mysql::getInstance($this->driver['mysql'][$key])->table($this->table);
	}

	/**
	 * 获取redis
	 * @param string $key
	 */
	protected function redis($key = 'master', $db = 0)
	{
		$driver = $this->driver['redis'][$redis];
		$driver['db'] = $db;
		return Redis::getInstance($this->driver['redis'][$redis]);
	}

	/**
	 * 读取配置信息
	 * @param array $key
	 * @return array
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
	
	/**
	 * 设置要查询的字段
	 * @param string 查询字符串列表
	 * @return \Driver\Mysql
	 */
	public function field($field)
	{
		$this->sql['field'] = $field;
		return $this;
	}
	
	/**
	 * 拼接where子句
	 * @return \Driver\Mysql
	 */
	public function where($condition)
	{
		$condition = $this->comCondition($condition, 'where');
		$this->sql['where'] = 'WHERE ' . implode(' AND ', $condition);
		return $this;
	}
	
	/**
	 * 拼接having子句
	 * @return \Driver\Mysql
	 */
	public function having($condition)
	{
		$condition = $this->comCondition($condition, 'having');
		$this->sql['having'] = 'HAVING ' . implode(' AND ', $condition);
		return $this;
	}
	
	/**
	 * order子句
	 * @return \Driver\Mysql
	 */
	public function order($order)
	{
		$this->sql['order'] = "ORDER BY {$order}";
		return $this;
	}
	
	/**
	 * group子句
	 * @return \Driver\Mysql
	 */
	public function group($group)
	{
		$this->sql['group'] = "GROUP BY {$group}";
		return $this;
	}
	
	/**
	 * limit子句
	 * @param int 偏移量
	 * @param int 个数
	 * @return \Driver\Mysql
	 */
	public function limit($offset, $number = NULL)
	{
		if(!$number)
		{
			$number = $offset;
			$offset = 0;
		}
		
		$this->sql['values'][':limit_offset'] = $offset;
		$this->sql['values'][':limit_number'] = $number;
		$this->sql["limit"] = "LIMIT :limit_offset, :limit_number";
		return $this;
	}
	
	/**
	 * 拼接条件子句
	 * @param array 键值对数组
	 * @param string where或者having
	 * @return array
	 */
	private final function comCondition($condition, $field)
	{
		// 字符串转义一下
		if (is_string($condition))
		{
			return array(addslashes($condition));
		}
	
		$conds = array();
		foreach ($condition as $key => $value)
		{
			// false null array() "" 的时候全部过滤,0不过滤
			if (!$value && !is_numeric($value))
			{
				continue;
			}
	
			// 去掉两边的空格
			$key = trim($key);
	
			// 操作类型
			$operations = array(' B', ' NL', ' L', ' N', ' <>', ' >', ' <', ' !=', ' !', ' &', ' ^', ' |', NULL);
			foreach($operations as $from=>$action)
			{
				if($location=strpos($key, $action))
				{
					$origin = $key;
					$key = substr($key, 0, $location);
					break;
				}
			}
	
			if($from==0)
			{
				// between...and
				$conds[] = "`{$key}` BETWEEN :{$key}from{$this->interval} AND :{$key}to{$this->interval}";
				$this->sql['values'][":{$key}from{$this->interval}"] = $value[0];
				$this->sql['values'][":{$key}to{$this->interval}"] = $value[1];
			}
			else if($key == 'OR')
			{
				// or
				$or = array();
				foreach ($value as $orKey=>$orValue)
				{
					$temp = is_array($orValue) ? $orValue : array($orKey => $orValue);
					$temp = $this->comCondition($temp, $field);
					$or[] = implode(' AND ', $temp);
				}
				$conds[] = "(".implode(" OR ", $or).")";
				continue;
			}
			else if(is_array($value))
			{
				// in | not in
				$expression = $from == 3 ? 'NOT IN' : 'IN';
				foreach ($value as $k => $val)
				{
					$temp[] = ":{$key}{$this->interval}_{$k}";
					$this->sql['values'][":{$key}{$this->interval}_{$k}"] = $val;
				}
				$conds[] = "`{$key}` {$expression}(" . implode(',', $temp) . ")";
			}
			else if (in_array($from, array(1, 2)))
			{
				// like
				$expression = $from == 2 ? 'LIKE' : 'NOT LIKE';
				$conds[] = "`{$key}` {$expression} :{$field}{$this->interval}";
				$this->sql['values'][":{$field}{$this->interval}"] = $value;
			}
			else if (in_array($from, array(4, 5, 6, 7, 8, 9, 10, 11)))
			{
				// > >= < <= != & ^ |
				$conds[] = "`{$key}`{$operations[$from]} :{$key}{$this->interval}";
				$this->sql['values'][":{$key}{$this->interval}"] = $value;
			}
			else
			{
				// =
				$conds[] = "`{$key}`=:{$key}{$this->interval}";
				$this->sql['values'][":{$key}{$this->interval}"] = $value;
			}
	
			$this->interval++;
		}
	
		return $conds;
	}
	
	/**
	 * 执行插入
	 * @param array 待插入的数据
	 * @param bool 多行返回插入的行数
	 * @return \Driver\Mysql
	 */
	public function insert(array $data, $rowCount=FALSE)
	{
		// 数据整理
		$data = count($data) != count($data, COUNT_RECURSIVE) ? $data : array($data);
		// 设置插入的键
		$this->sql['keys'] = array_keys($data[0]);
		// 设置插入的值
		foreach ($data as $key => $insert)
		{
			$prepare = array();
			foreach ($this->sql['keys'] as $prev)
			{
				$placeholder = ":{$prev}_{$key}"; // 占位符号
				$prepare[] = $placeholder;
				$this->sql['values'][$placeholder] = array_shift($insert);
			}
			$this->sql['prepare'][] = sprintf("(%s)", implode(',', $prepare));
		}
		// 预处理sql语句
		$preKeys = sprintf("(`%s`)", implode('`,`', $this->sql['keys']));
		// 插入对应的预处理值
		$preValues = implode(',', $this->sql['prepare']);
		// 插入语句
		$sql = "INSERT INTO {$this->sql['table']}{$preKeys} VALUES {$preValues}";
		// 执行sql语句
		$this->query($sql, $this->sql['values']);
		// 结果返回
		return $rowCount ? $this->rowCount() : $this->lastInsertId();
	}
	
	/**
	 * 执行删除
	 * @return \Driver\Mysql;
	 */
	public final function delete()
	{
		// 拼接sql语句
		$sql = "DELETE FROM {$this->sql['table']} {$this->sql['where']} {$this->sql['limit']}";
		// 执行sql语句
		$this->query($sql, $this->sql['values']);
		// 返回结果
		return $this->rowCount();
	}
	
	/**
	 * 执行查询
	 * @return \Driver\Mysql
	 */
	protected function select()
	{
		// 拼接sql语句
		$sql = "SELECT {$this->sql['field']} FROM {$this->sql['table']} {$this->sql['where']} {$this->sql['group']} {$this->sql['having']} {$this->sql['order']} {$this->sql['limit']}";
		// 执行sql语句
		$this->query($sql, $this->sql['values']);
		// 返回类型
		return $this;
	}
	
	/**
	 * 执行更新
	 * @param array 键值对数组
	 * @return \Driver\Mysql;
	 */
	public final function update(array $update)
	{
		foreach ($update as $key => $val)
		{
			// 自增等系列处理
			if (stripos($val, $key) !== FALSE)
			{
				foreach (array('+','-','*','/','^','&','|','!') as $opeartion)
				{
					if (strpos($val, $opeartion))
					{
						$temp = explode($opeartion, $val);
						break;
					}
				}
				$set[] = "`{$key}`={$temp[0]}{$opeartion}:{$key}";
				$this->sql['values'][":{$key}"] = $temp[1];
			}
			else
			{
				// 普通赋值
				$set[] = "`{$key}`=:{$key}";
				$this->sql['values'][":{$key}"] = $val;
			}
		}
		// set语句
		$set = implode(',', $set);
		// sql语句
		$sql = "UPDATE {$this->sql['table']} SET {$set} {$this->sql['where']} {$this->sql['order']} {$this->sql['limit']}";
		// 执行sql语句
		$this->query($sql, $this->sql['values']);
		// 返回当前对象
		return $this->rowCount();
	}
	
	/**
	 * 重置条件查询
	 * @return void
	 */
	protected function resetSql()
	{
		$this->sql = array(
				'field' => '*',
				'where' => NULL,
				'group' => NULL,
				'having' => NULL,
				'order' => NULL,
				'limit' => NULL,
				'prepare' => NULL,
				'keys' => NULL,
				'values' => NULL
		);
	}
}

