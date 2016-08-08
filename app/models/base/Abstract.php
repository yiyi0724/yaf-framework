<?php

/**
 * 模型基类（基于mysql数据库）
 * @author enychen
 */
namespace base;

use \database\PDO;
use \Yaf\Config\Ini;

abstract class AbstractModel {

	/**
	 * 配置信息
	 * @var \Yaf\Config\Ini
	 */
	protected static $config = NULL;

	/**
	 * 配置适配器名称
	 * @var string
	 */
	protected $adapter = 'master';

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
	public function __construct() {
		static::setConfig();
		$this->setDatabase($this->adapter);
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
	 * @return void
	 */
	protected static final function setConfig() {
		if(!static::$config) {
			static::$config = new Ini(CONF_PATH . 'driver.ini', \YAF\ENVIRON);
		}
	}

	/**
	 * 获取驱动配置信息
	 * @param string $key 键
	 * @return \Yaf\Config\Ini
	 */
	protected static final function getConfig($key) {
		return static::$config->get($key);
	}

	/**
	 * 获取数据库驱动对象
	 * @param string $adapter 配置适配器名称
	 * @return void
	 */
	protected final function setDatabase($adapter) {
		$config = static::getConfig("database.{$adapter}");
		$this->database = PDO::getInstance($config->type, $config->host, $config->port, 
			$config->dbname, $config->charset, $config->username, $config->password);

		// 设置调试模式
		if(\Yaf\ENVIRON != 'product') {
			$this->database->setIsDebug(TRUE);
		}
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
		$config = static::getConfig("redis.{$adapter}");
		return \Storage\Redis::getInstance($config->host, $config->port, $config->db, 
			$config->auth, $config->timeout, $config->options);
	}

	/**
	 * 开启事务
	 * @return boolean
	 */
	public final function beginTransaction() {
		return $this->getDatabase()->beginTransaction();
	}

	/**
	 * 提交事务
	 * @return boolean
	 */
	public final function commitTransaction() {
		return $this->getDatabase()->commitTransaction();
	}

	/**
	 * 回滚事务
	 * @return boolean
	 */
	public final function rollbackTransaction() {
		return $this->getDatabase()->rollbackTransaction();
	}

	/**
	 * 判断是否在事务中
	 * @return bool
	 */
	public final function inTransaction() {
		return $this->getDatabase()->inTransaction();
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
	public final function join($table, $on, $type = 'LEFT') {
		$this->sql['join'] = "{$type} JOIN {$table} ON {$on}";
		return $this;
	}

	/**
	 * 拼接where子句
	 * @return \Base\AbstractModel $this 返回当前对象进行连贯操作
	 */
	public final function where() {
		$result = $placeholder = array();
		$args = func_get_args();
		if(count($args) >= 0) {
			if(count($args) > 1) {
				preg_match_all('/\:[a-zA-Z][a-zA-Z0-9]*/', $args[0], $result);
				$result = $result[0];
				for($i=0, $len=count($result); $i<$len; $i++) {
					$placeholder["{$result[$i]}"] = $args[$i+1];
				}
			}
			$this->sql['where'] = "WHERE {$args[0]}";
			$this->sql['values'] = array_merge($this->sql['values'], $placeholder);
		}

		return $this;
	}

	/**
	 * 拼接having子句
	 * @return \Base\AbstractModel $this 返回当前对象进行连贯操作
	 */
	public final function having() {
		$result = $placeholder = array();
		$args = func_get_args();
		if(count($args) >= 0) {
			if(count($args) > 1) {
				preg_match_all('/\:[a-zA-Z][a-zA-Z0-9]*/', $args[0], $result);
				$result = $result[0];
				for($i=0, $len=count($result); $i<$len; $i++) {
					$placeholder["{$result[$i]}"] = $args[$i+1];
				}
			}
			$this->sql['having'] = "HAVING {$args[0]}";
			$this->sql['values'] = array_merge($this->sql['values'], $placeholder);
		}

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
		$this->sql['values'][':limit_offset'] = $offset;
		$this->sql['values'][':limit_number'] = $limit;
		$this->sql["limit"] = "LIMIT :limit_offset, :limit_number";
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
	 * 执行插入
	 * @param array $data 待插入的数据
	 * @return int 返回上次插入的id
	 */
	public final function insert(array $data) {
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
		$sql = "INSERT INTO {$this->getTable()}{$preKeys} VALUES {$preValues}";
		// 执行sql语句
		$this->query($sql, $this->sql['values']);
		// 清空数据
		$this->resetSql();
		// 结果返回
		return $this->getLastInsertId();
	}

	/**
	 * 执行删除
	 * @return int 影响的行数
	 */
	public final function delete() {
		// 拼接sql语句
		$sql = "DELETE FROM {$this->getTable()} {$this->sql['where']}{$this->sql['order']}{$this->sql['limit']}";
		// 执行sql语句
		$this->query($sql, $this->sql['values']);
		// 清空数据
		$this->resetSql();
		// 返回结果
		return $this->getAffectRowCount();
	}

	/**
	 * 执行查询,返回对象进行fetch操作
	 * @param boolean $clear 是否清空条件信息，默认是
	 * @return AbstractModel $this 返回当前对象进行连贯操作
	 */
	public final function select($clear = TRUE) {
		// 局部释放变量
		extract($this->sql);
		// 拼接sql语句
		$sql = "SELECT {$field} FROM {$this->getTable()} {$join} {$where} {$group} {$having} {$order} {$limit} {$lock}";
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
		$sql = "UPDATE {$this->getTable()} SET {$sets} {$this->sql['where']} {$this->sql['order']} {$this->sql['limit']}";
		// 执行sql语句
		$this->query($sql, $this->sql['values']);
		// 清空数据
		$this->resetSql();
		// 返回当前对象
		return $this->getAffectRowCount();
	}

	/**
	 * 执行原生sql语句
	 * @param string $sql sql语句
	 * @param array $params 参数
	 * @return AbstractModel $this 返回当前对象进行连贯操作
	 */
	public function query($sql, array $params = array()) {
		$this->getDatabase()->query($sql, $params);
		return $this;
	}

	/**
	 * 重置查询
	 * @return void
	 */
	protected final function resetSql() {
		$this->sql = array(
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
	}

	/**
	 * 获取上次插入的id值
	 * @return int
	 */
	public function getLastInsertId() {
		return $this->getDatabase()->getLastInsertId();
	}

	/**
	 * 获取影响的行数
	 * @return int
	 */
	public function getAffectRowCount() {
		return $this->getDatabase()->getAffectRowCount();
	}

	/**
	 * 查询结果获取全部
	 * @return array
	 */
	public function fetchAll() {
		return $this->getDatabase()->fetchAll();
	}

	/**
	 * 查询结果获取一行
	 * @return array
	 */
	public function fetchRow() {
		return $this->getDatabase()->fetchRow();
	}

	/**
	 * 查询结果获取一个值
	 * @return string|null|false|number
	 */
	public function fetchOne() {
		return $this->getDatabase()->fetchOne();
	}

	/**
	 * 分页获取信息
	 * @param int $page 当前页
	 * @param int $number 每页几条
	 * @param int @button 按钮个数
	 * @return array 分页信息
	 */
	public function pagitor($page = 1, $number = 15, $button = 6) {
		// 获取本页数据
		$this->limit(abs($page - 1) * $number, $number);
		$pagitor['list'] = $this->select(FALSE)->fetchAll();

		// 获取分页数量
		$this->field('COUNT(*)');
		$total = $this->select()->fetchOne();

		// 输出分页
		$pagitorLib = new \network\Pagitor($page, $total);
		$pagitorLib->setPerNumber($number);
		$pagitor = $pagitorLib->showCenter();

		return $pagitor;
	}

	public function supplement() {
		
	}
}