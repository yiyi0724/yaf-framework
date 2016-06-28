<?php

/**
 * 模型基类
 * @author enychen
 */
namespace Base;

use \Yaf\Config\Ini;

abstract class AbstractModel {

	/**
	 * 配置信息
	 * @var \Yaf\Config\Ini
	 */
	protected $config = NULL;

	/**
	 * 数据库驱动适配器
	 * @var \Database\Adapter
	 */
	protected $database = 'PDO';

	/**
	 * 表名
	 * @var string
	 */
	protected $table = NULL;

	/**
	 * 查询条件列表
	 * @var array
	 */
	protected $sql = array(
		'field'=>'*',
		'join'=>NULL,
		'where'=>NULL,
		'group'=>NULL,
		'having'=>NULL,
		'order'=>NULL,
		'limit'=>NULL,
		'lock'=>NULL,
		'prepare'=>NULL,
		'keys'=>NULL,
		'values'=>array()
	);

	/**
	 * 构造函数,加载配置
	 * @return void
	 */
	public final function __construct($adapter = 'master') {
		$this->setDriverConfig();
		$this->setDatabase($adapter);
	}

	/**
	 * 获取表名称
	 * @return string
	 */
	public final function getTable() {
		return $this->table;
	}

	/**
	 * 设置驱动配置信息
	 * @return AbstractModel
	 */
	protected final function setDriverConfig() {
		$this->config = new Ini(CONF_PATH . 'driver.ini', \YAF\ENVIRON);
		return $this;
	}

	/**
	 * 获取驱动配置信息
	 * @param string $key 键
	 * @param mixed $default 默认值
	 * @return \Yaf\Config\Ini
	 */
	protected final function getDriverConfig($key, $default = NULL) {
		return $this->config->get($key);
	}

	/**
	 * 获取数据库驱动对象
	 * @param string $adapter 配置适配器名称
	 * @return void
	 */
	protected final function setDatabase($adapter) {
		$config = $this->getDriverConfig("database.{$adapter}");
		$driver = "\\Database\\{$this->database}";
		$this->database = $dbDriver::getInstance($config->type, $config->host, $config->port, 
			$config->dbname, $config->charset, $config->username, $config->password);
	}

	/**
	 * 获取数据库驱动对象
	 * @return \Database\Adapter
	 */
	protected final function getDatabase() {
		return $this->database;
	}

	/**
	 * 获取redis对象
	 * @param string $adapter 适配器名称，默认master
	 * @return \Storage\Redis
	 */
	protected final function getRedis($adapter = 'master') {
		$config = $this->getDriverConfig("redis.{$adapter}");
		return \Storage\Redis::getInstance($config->host, $config->port, $config->db, 
			$config->auth, $config->timeout, $config->options);
	}

	/**
	 * 设置要查询的字段
	 * @param string $field 查询字符串列表
	 * @return \Base\AbstractModel $this 返回当前对象进行连贯操作
	 */
	public final function field($field) {
		$this->sql['field'] = $field;
		return $this;
	}

	/**
	 * 进行连接操作
	 * @param string $table 要连接的表名
	 * @param string $on 连接on条件
	 * @param string $type LEFT|RIGHT|INNER 三种连接方式
	 * @return \Base\AbstractModel $this 返回当前对象进行连贯操作
	 */
	public function join($table, $on, $type = 'LEFT') {
		$this->sql['join'] = "{$type} JOIN {$table} ON {$on}";
		return $this;
	}

	/**
	 * 拼接where子句
	 * @params string|array $condition 要拼接的条件
	 * @return \Base\AbstractModel $this 返回当前对象进行连贯操作
	 */
	public final function where($condition) {
		$this->sql['where'] = "WHERE {$this->comCondition($condition)}";
		return $this;
	}

	/**
	 * 拼接having子句
	 * @params string|array $condition 要拼接的条件
	 * @return \Base\AbstractModel $this 返回当前对象进行连贯操作
	 */
	public final function having($condition) {
		$this->sql['having'] = "HAVING {$this->comCondition($condition)}";
		return $this;
	}

	/**
	 * 拼接order子句
	 * @param string $order 排序字符串
	 * @return \Base\AbstractModel $this 返回当前对象进行连贯操作
	 */
	public final function order($order) {
		$this->sql['order'] = "ORDER BY {$order}";
		return $this;
	}

	/**
	 * 拼接group子句
	 * @param string $group 分组字符串
	 * @return \Base\AbstractModel $this 返回当前对象进行连贯操作
	 */
	public final function group($group) {
		$this->sql['group'] = "GROUP BY {$group}";
		return $this;
	}

	/**
	 * 拼接limit子句
	 * @param int $offset 偏移量
	 * @param int $limit 个数，不传表示偏移量为0
	 * @return \Base\AbstractModel $this 返回当前对象进行连贯操作
	 */
	public final function limit($offset, $limit = NULL) {
		if(!$limit) {
			$limit = $offset;
			$offset = 0;
		}
		$this->sql['values'][':limitOffset'] = $offset;
		$this->sql['values'][':limitNumber'] = $limit;
		$this->sql["limit"] = "LIMIT :limitOffset, :limitNumber";
		return $this;
	}

	/**
	 * 加锁，在sql语句后面执行for update，必须开启事务才有效果
	 * @return \Base\AbstractModel $this 返回当前对象进行连贯操作
	 */
	public final function lock() {
		$this->sql['lock'] = 'FOR UPDATE';
		return $this;
	}

	/**
	 * 拼接条件子句
	 * @param array 键值对数组
	 * @param string where或者having
	 * @return string
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
			$operations = array(
				' B', ' NL', ' L', ' N', ' <>', ' >=', ' <=', ' >', ' <', ' !=', ' !', ' &', ' ^', ' |', NULL
			);
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
		$data = count($data) != count($data, COUNT_RECURSIVE) ? $data : array(
			$data
		);
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
		$sql = "INSERT INTO {$this->table}{$preKeys} VALUES {$preValues}";
		// 执行sql语句
		$this->query($sql, $this->sql['values']);
		// 清空数据
		$this->resetSql();
		// 结果返回
		return $rowCount ? $this->rowCount() : $this->lastInsertId();
	}

	/**
	 * 执行删除
	 * @return int 影响的行数;
	 */
	public final function delete() {
		// 拼接sql语句
		$sql = "DELETE FROM {$this->table} {$this->sql['where']}{$this->sql['order']}{$this->sql['limit']}";
		// 执行sql语句
		$this->query($sql, $this->sql['values']);
		// 清空数据
		$this->resetSql();
		// 返回结果
		return $this->rowCount();
	}

	/**
	 * 执行查询,返回对象进行fetch操作
	 * @param string $clear 清空条件信息
	 * @return \Database\Adapter
	 */
	public final function select($clear = TRUE) {
		// 局部释放变量
		extract($this->sql);
		// 拼接sql语句
		$sql = "SELECT {$field} FROM {$this->table} {$join} {$where} {$group} {$having} {$order} {$limit} {$other}";
		// 执行sql语句
		$this->query($sql, $values);
		// 清空数据
		$clear and $this->resetSql();
		// 返回数据库操作对象
		return $this->db;
	}

	/**
	 * 执行查询计划,输出<table></table>
	 * @return void
	 */
	public final function explain() {
		// 局部释放变量
		extract($this->sql);
		// 拼接sql语句
		$sql = "EXPLAIN SELECT {$field} FROM {$this->table} {$join} {$where} {$group} {$having} {$order} {$limit} {$lock}";
		// 执行sql语句
		$this->query($sql, $values);
		// 清空数据
		$this->resetSql();
		
		// 返回数据库操作对象
		$results = $this->db->fetchAll();
		$keys = array_keys((array)$results[0]);
		$table = '<style>table{width:100%;border-collapse: collapse;}th,td{border:1px solid #ccc;padding:5px 10px}td{text-align:center;padding:10px;}</style>';
		$table .= '<table>';
		$table .= '<tr><th>' . implode('</th><th>', $keys) . '</th></tr>';
		foreach($results as $result) {
			$result = (array)$result;
			$table .= '<tr><td>' . implode('</td><td>', $result) . '</td></tr>';
		}
		$table .= '</table>';
		exit($table);
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
		$sql = "UPDATE {$this->table} SET {$sets} {$this->sql['where']} {$this->sql['order']} {$this->sql['limit']}";
		// 执行sql语句
		$this->query($sql, $this->sql['values']);
		// 清空数据
		$this->resetSql();
		// 返回当前对象
		return $this->rowCount();
	}

	/**
	 * 重置查询
	 * @return void
	 */
	protected final function resetSql() {
		$this->sql = array(
			'field'=>'*', 'join'=>NULL, 'where'=>NULL, 'group'=>NULL, 'having'=>NULL, 'order'=>NULL, 'limit'=>NULL, 'lock'=>NULL, 'prepare'=>NULL, 'keys'=>NULL, 'values'=>array()
		);
	}

	/**
	 * 执行原生sql语句
	 * @param string $sql sql语句
	 * @param array $params 参数
	 * @return
	 */
	public function query($sql, array $params = array()) {
		$this->setDb();
		return $this->getDb->query($sql, $params);
	}

	/**
	 * 分页获取信息
	 * @param int 			$page 	当前页
	 * @param int 			$number 每页几条
	 * @return array 分页信息
	 */
	public function paging($page = 1, $number = 15) {
		// 获取本页数据
		$this->limit(abs($page - 1) * $number, $number);
		$lists = $this->select()->fetchAll();
		
		// 获取分页数量
		$this->field('COUNT(*)');
		$total = $this->select(FALSE)->fetchColumn();
		
		// 输出分页
		$pagitor = \Network\Page::showCenter($page, $number, $total, 6);
		$pagitor['lists'] = $lists;
		
		return $pagitor;
	}
}