<?php

/**
 * mysql数据库类
 * @author enychen
 * @version 1.0
 */
namespace Driver;

class Mysql extends Driver {

	/**
	 * pdo对象
	 * @var \Pdo
	 */
	protected $pdo;

	/**
	 * 预处理对象
	 * @var \PDOStatement
	 */
	protected $stmt;

	/**
	 * 禁止直接创建构造函数
	 * @param array $driver 驱动选项，包含 host | port | dbname | charset | username | password
	 * @return void
	 */
	protected function __construct(array $driver) {
		// 数据库连接信息
		$dsn = "mysql:host={$driver['host']};port={$driver['port']};dbname={$driver['dbname']};charset={$driver['charset']}";
		// 驱动选项
		$options = array(
			\PDO::ATTR_ERRMODE=>\PDO::ERRMODE_EXCEPTION, 
			\PDO::ATTR_TIMEOUT=>30
		);
		
		// 创建数据库驱动对象
		$this->pdo = new \PDO($dsn, $driver['username'], $driver['password'], $options);
	}

	/**
	 * 执行sql语句
	 * @param string $sql sql语句
	 * @param array $params 参数
	 * @return \Driver\Mysql
	 */
	public function query($sql, array $params = array()) {
		// 预处理语句
		$this->stmt = $this->pdo->prepare($sql);
		// 参数绑定
		$this->bindValue($params);
		// sql语句执行
		$this->stmt->execute();
		// 结果解析成数组
		$this->stmt->setFetchMode(\PDO::FETCH_ASSOC);
		// 返回当前对象
		return $this;
	}

	/**
	 * 调试sql语句
	 * @param string $sql sql语句
	 * @param array $params 参数
	 * @return void
	 */
	public function debug($sql, array $params = array()) {
		echo "<pre>placeholder sql: {$sql}<hr/>";
		print_r($params);
		foreach($params as $key=>$placeholder) {
			is_string($placeholder) and ($placeholder = "'{$placeholder}'");
			$start = strpos($sql, $key);
			$end = strlen($key);
			$sql = substr_replace($sql, $placeholder, $start, $end);
		}
		exit("<hr/> origin sql: {$sql}</pre>");
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
	 * 简单回调pdo对象方法
	 * @param string $method 函数名
	 * @return mixed
	 */
	public function __call($method, $args) {
		switch($method) {
			case 'beginTransaction':
			case 'inTransaction':
			case 'commit':
			case 'rollback':
			case 'lastInsertId':
				$result = $this->pdo->$method();
				break;
			case 'fetchAll':
			case 'fetch':
			case 'fetchColumn':
			case 'rowCount':
				$result = $this->stmt->$method();
				break;
			default:
				throw new \PDOException("Call to undefined method Mysql::{$method}()");
		}
		
		return $result;
	}
}