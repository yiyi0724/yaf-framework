<?php

/**
 * 数据库类(基于pdo)
 * @author enychen
 * @version 1.0
 */
namespace Database;

class Mysql extends Assembly {
	
	/**
	 * 附加的查询条件
	 * @var array
	 */
	protected $sql = array(
		'field'=>'*',
		'table'=>NULL,
		'join'=>NULL,
		'where'=>NULL,
		'group'=>NULL,
		'having'=>NULL,
		'order'=>NULL,
		'limit'=>NULL,
		'other'=>NULL,
		'prepare'=>NULL,
		'keys'=>NULL,
		'values'=>array()
	);
	
	/**
	 * 设置要查询的字段
	 * @param string $field 查询字符串列表
	 * @return \Driver\Mysql
	 */
	public final function field($field) {
		$this->sql['field'] = $field;
		return $this;
	}
	
	/**
	 * 设置表名
	 * @param string $from 表名
	 * @return \Driver\Mysql
	 */
	public final function table($table) {
		$this->sql['table'] = $table;
		return $this;
	}
	
	/**
	 * 进行连接操作
	 * @param string $table 要连接的表名
	 * @param string $on 连接on条件
	 * @param string $type left|right|inner 三种连接方式
	 * @return \Driver\Mysql
	 */
	public function join($table, $on, $type = 'left') {
		$this->sql['join'] = "{$type} join {$table} on {$on}";
		return $this;
	}
	
	/**
	 * 拼接where子句
	 * @params string|array $condition 要拼接的条件
	 * @return \Driver\Mysql
	 */
	public final function where($condition) {
		$this->sql['where'] = "WHERE {$this->comCondition($condition)}";
		return $this;
	}
	
	/**
	 * 拼接having子句
	 * @params string|array $condition 要拼接的条件
	 * @return \Driver\Mysql
	 */
	public final function having($condition) {
		$this->sql['having'] = "HAVING {$this->comCondition($condition)}";
		return $this;
	}
	
	/**
	 * 拼接order子句
	 * @param string $order 排序字符串
	 * @return \Driver\Mysql
	 */
	public final function order($order) {
		$this->sql['order'] = "ORDER BY {$order}";
		return $this;
	}
	
	/**
	 * 拼接group子句
	 * @param string $group 分组字符串
	 * @return \Driver\Mysql
	 */
	public final function group($group) {
		$this->sql['group'] = "GROUP BY {$group}";
		return $this;
	}
	
	/**
	 * 拼接limit子句
	 * @param int $offset 偏移量
	 * @param int $limit 个数
	 * @return \Driver\Mysql
	 */
	public final function limit($offset, $limit = NULL) {
		if(!$limit) {
			$limit = $offset;
			$offset = 0;
		}
		$this->sql['values'][':limit_offset'] = $offset;
		$this->sql['values'][':limit_number'] = $limit;
		$this->sql["limit"] = "LIMIT :limit_offset, :limit_number";
		return $this;
	}
	
	/**
	 * 拼接其他附加条件
	 * @param string $other 其他附加条件，如for update
	 * @return \Driver\Mysql
	 */
	public final function other($other) {
		$this->sql['other'] = $other;
		return $this;
	}
	
	/**
	 * 拼接条件子句
	 * @param array 键值对数组
	 * @param string where或者having
	 * @return array|string
	 */
	protected final function comCondition($condition) {
		static $interval;
	
		// 字符串转义一下
		if(is_string($condition)) {
			return addslashes($condition);
		}
	
		$conds = array();
		foreach($condition as $key=>$value) {
			// false null array() "" 的时候全部过滤,0不过滤
			if(!$value && !is_numeric($value)) {
				continue;
			}
				
			// 去掉两边的空格
			$key = trim($key);
				
			// 操作类型
			$operations = array(' B', ' NL', ' L', ' N', ' <>', ' >=', ' <=', ' >', ' <', ' !=', ' !', ' &', ' ^', ' |', NULL);
			foreach($operations as $from=>$action) {
				if($location = strpos($key, $action)) {
					$origin = $key;
					$key = substr($key, 0, $location);
					break;
				}
			}
				
			if($from == 0) {
				// between...and
				$conds[] = "`{$key}` BETWEEN :{$key}from{$interval} AND :{$key}to{$interval}";
				$this->sql['values'][":{$key}from{$interval}"] = $value[0];
				$this->sql['values'][":{$key}to{$interval}"] = $value[1];
			} else if($key == 'OR') {
				// or
				$or = array();
				foreach($value as $orKey=>$orValue) {
					$temp = is_array($orValue) ? $orValue : array(
						$orKey=>$orValue
					);
					$or[] = $this->comCondition($temp);
				}
				$conds[] = "(" . implode(" OR ", $or) . ")";
				continue;
			} else if(is_array($value)) {
				// in | not in
				$expression = $from == 3 ? 'NOT IN' : 'IN';
				foreach($value as $k=>$val) {
					$temp[] = ":{$key}{$interval}_{$k}";
					$this->sql['values'][":{$key}{$interval}_{$k}"] = $val;
				}
				$conds[] = "`{$key}` {$expression}(" . implode(',', $temp) . ")";
			} else if(in_array($from, array(1, 2))) {
				// like
				$expression = $from == 2 ? 'LIKE' : 'NOT LIKE';
				$conds[] = "`{$key}` {$expression} :{$key}{$interval}";
				$this->sql['values'][":{$key}{$interval}"] = $value;
			} else if(in_array($from, array(4, 5, 6, 7, 8, 9, 10, 11))) {
				// > >= < <= != & ^ |
				$conds[] = "`{$key}`{$operations[$from]} :{$key}{$interval}";
				$this->sql['values'][":{$key}{$interval}"] = $value;
			} else {
				// =
				$conds[] = "`{$key}`=:{$key}{$interval}";
				$this->sql['values'][":{$key}{$interval}"] = $value;
			}
				
			$interval++;
		}
	
		return implode(' AND ', $conds);
	}
	
	/**
	 * 执行插入
	 * @param array 待插入的数据
	 * @param bool 多行返回插入的行数
	 * @return int 返回上次插入的id 或者 影响行数
	 */
	public final function insert(array $data, $rowCount = FALSE) {
		// 数据整理
		$data = count($data) != count($data, COUNT_RECURSIVE) ? $data : array($data);
		// 设置插入的键
		$this->sql['keys'] = array_keys($data[0]);
		// 设置插入的值
		foreach($data as $key=>$insert) {
			$prepare = array();
			foreach($this->sql['keys'] as $prev) {
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
		// 清空数据
		$this->resetSql();
		// 结果返回
		return $rowCount ? $this->pdo->rowCount() : $this->pdo->lastInsertId();
	}
	
	/**
	 * 执行删除
	 * @return int 影响的行数;
	 */
	public final function delete() {
		// 拼接sql语句
		$sql = "DELETE FROM {$this->sql['table']} {$this->sql['where']}{$this->sql['order']}{$this->sql['limit']}";
		// 执行sql语句
		$this->query($sql, $this->sql['values']);
		// 清空数据
		$this->resetSql();
		// 返回结果
		return $this->pdo->rowCount();
	}
	
	/**
	 * 执行查询,返回对象进行fetch操作
	 * @param string $clear 清空条件信息
	 * @return \Driver\Mysql
	 */
	public final function select($clear = TRUE) {
		// 局部释放变量
		extract($this->sql);
		// 拼接sql语句
		$sql = "SELECT {$field} FROM {$table} {$join} {$where} {$group} {$having} {$order} {$limit} {$other}";
		// 执行sql语句
		$this->query($sql, $values);
		// 清空数据
		$clear and $this->resetSql();
		// 返回数据库操作对象
		return $this;
	}
	
	/**
	 * 执行更新
	 * @param array $update 键值对数组
	 * @return int 影响的行数
	 */
	public final function update(array $update) {
		foreach($update as $key=>$val) {
			if(preg_match('/([+|\-|\*|\/|%|&|\||\!|\^])(\d+)/', $val, $result)) {
				// 自增等系列处理
				$set = "`{$key}`=`{$key}`{$result[1]}:{$key}";
				$val = $result[2];
			} else {
				// 默认处理方式
				$set = "`{$key}`=:{$key}";
			}
				
			$sets[] = $set;
			$this->sql['values'][":{$key}"] = $val;
		}
		// set语句
		$sets = implode(',', $sets);
		// sql语句
		$sql = "UPDATE {$this->sql['table']} SET {$sets} {$this->sql['where']} {$this->sql['order']} {$this->sql['limit']}";
		// 执行sql语句
		$this->query($sql, $this->sql['values']);
		// 清空数据
		$this->resetSql();
		// 返回当前对象
		return $this->pdo->rowCount();
	}
	
	/**
	 * 重置查询
	 * @return void
	 */
	protected final function resetSql() {
		$this->sql = array(
			'field'=>'*',
			'table'=>NULL,
			'join'=>NULL,
			'where'=>NULL,
			'group'=>NULL,
			'having'=>NULL,
			'order'=>NULL,
			'limit'=>NULL,
			'other'=>NULL,
			'prepare'=>NULL,
			'keys'=>NULL,
			'values'=>array()
		);
	}
}