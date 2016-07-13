<?php

/**
 * PDO操作驱动
 * @author enychen
 * @version 1.0
 */
namespace database;

class PDO extends Adapter {

	/**
	 * 对象池
	 * @var array
	 */
	protected static $pool;

	/**
	 * pdo对象
	 * @var \PDO
	 */
	protected $pdo;

	/**
	 * 预处理对象
	 * @var \PDOStatement
	 */
	protected $stmt;

	/**
	 * 禁止直接创建构造函数
	 * @param string $dsn 	   数据库连接dsn信息
	 * @param string $username 数据库连接用户
	 * @param string $password 数据库连接密码
	 */
	protected final function __construct($dsn, $username, $password) {
		$options = array(
			\PDO::ATTR_ERRMODE=>\PDO::ERRMODE_EXCEPTION, \PDO::ATTR_TIMEOUT=>30
		);
		$this->pdo = new \PDO($dsn, $username, $password, $options);
	}

	/**
	 * 单例模式创建连接池对象
	 * @param string $type	   	数据库类型，如mysql,sqlite
	 * @param string $host	  	数据库地址
	 * @param string $port		数据库端口
	 * @param string $charset	数据库字符集
	 * @param string $username 	数据库连接用户
	 * @param string $password 	数据库连接密码
	 * @return \Database\PDO
	 */
	public static function getInstance($type, $host, $port, $dbname, $charset, $username, $password) {
		$dsn = "{$type}:host={$host};port={$port};dbname={$dbname};charset={$charset}";
		if(empty(static::$pool[$dsn])) {
			static::$pool[$dsn] = new static($dsn, $username, $password);
		}
		return static::$pool[$dsn];
	}

	/**
	 * 执行sql语句
	 * @throws \PDOException
	 * @param string $sql    		sql语句
	 * @param array  $params 		预绑定参数数组
	 * @return \database\driver\PDO
	 */
	public function query($sql, array $params = array()) {
		try {
			$this->stmt = $this->pdo->prepare($sql);
			$this->bindValue($params);
			$this->stmt->execute();
			$this->stmt->setFetchMode(\PDO::FETCH_OBJ);
		} catch(\PDOException $e) {
			$this->getIsDebug() and $this->debug($sql, $params);
			throw $e;
		}
		return $this;
	}

	/**
	 * 调试sql语句
	 * @param string $sql 	sql语句
	 * @param array $params 参数
	 * @return void
	 */
	public function debug($sql, array $params = array()) {
		echo '<h1>Database SQL Error:</h1>';
		echo "{$sql}<hr/><pre>";
		print_r($params);
		foreach($params as $key=>$placeholder) {
			is_string($placeholder) and ($placeholder = "'{$placeholder}'");
			$start = strpos($sql, $key);
			$end = strlen($key);
			$sql = substr_replace($sql, $placeholder, $start, $end);
		}
		exit("<hr/>{$sql}</pre>");
	}

	/**
	 * 参数与数据类型绑定
	 * @param array 值绑定
	 * @return void
	 */
	private function bindValue($params) {
		foreach($params as $key=>$value) {
			// 数据类型选择
			switch(TRUE) {
				case is_int($value):
					$type = \PDO::PARAM_INT;
					break;
				case is_bool($value):
					$type = \PDO::PARAM_BOOL;
					break;
				case is_null($value):
					$type = \PDO::PARAM_NULL;
					break;
				default:
					$type = \PDO::PARAM_STR;
			}
			// 参数绑定
			$this->stmt->bindValue($key, $value, $type);
		}
	}

	/**
	 * 开启事务
	 * @return boolean
	 */
	public function beginTransaction() {
		return $this->pdo->beginTransaction();
	}

	/**
	 * 判断是否在事务内
	 * @return boolean
	 */
	public function inTransaction() {
		return $this->pdo->inTransaction();
	}

	/**
	 * 提交事务
	 * @return boolean
	 */
	public function commitTransaction() {
		return $this->pdo->commit();
	}

	/**
	 * 回滚事务
	 * @return boolean
	 */
	public function rollbackTransaction() {
		return $this->pdo->rollback();
	}

	/**
	 * 获取上一次插入的id
	 * @return int
	 */
	public function getLastInsertId() {
		return $this->pdo->lastInsertId();
	}

	/**
	 * 获取影响的行数
	 * @return int
	 */
	public function getAffectRowCount() {
		return $this->stmt->rowCount();
	}

	/**
	 * 获取select的所有结果
	 * @return array
	 */
	public function fetchAll() {
		return $this->stmt->fetchAll(\PDO::FETCH_ASSOC);
	}

	/**
	 * 获取select的一行结果
	 * @return array
	 */
	public function fetchRow() {
		return $this->stmt->fetch(\PDO::FETCH_ASSOC);
	}

	/**
	 * 获取select的一个结果
	 * @return string
	 */
	public function fetchOne() {
		return $this->stmt->fetchColumn(\PDO::FETCH_ASSOC);
	}
}