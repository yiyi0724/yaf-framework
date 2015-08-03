<?php
/**
 * mysql数据库类
 * @author enychen
 */
namespace Driver;

class Mysql
{

	/**
	 * 数据库连接池
	 * @var array
	 */
	private static $instance;

	/**
	 * 当前的pdo对象
	 * @var \PDO
	 */
	private $pdo;

	/**
	 * 预处理对象
	 * @var \PDOStatement
	 */
	private $stmt;

	/**
	 * 禁止直接new对象
	 * @param array 数组配置
	 * @return void
	 */
	private final function __construct($driver)
	{
		// 数据库连接信息
		$dsn = "mysql:host={$driver['host']};port={$driver['port']};dbname={$driver['dbname']};charset={$driver['charset']}";
		// 驱动选项
		$options = array( 
			\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION, // 如果出现错误抛出错误警告
			\PDO::ATTR_ORACLE_NULLS => \PDO::NULL_TO_STRING, // 把所有的NULL改成""
			\PDO::ATTR_TIMEOUT => 30
		); // 超时时间

		// 创建数据库驱动对象
		$this->pdo = new \PDO($dsn, $driver['username'], $driver['password'], $options);
	}

	/**
	 * 禁止克隆对象
	 * @return void
	 */
	private final function __clone()
	{
	}

	/**
	 * 单例模式创建数据库连接池对象
	 * @param array 数组配置,包含的key必须有 host,port,dbname,charset,username,password
	 * @return \Driver\Mysql
	 */
	public static function getInstance(array $driver)
	{
		// 计算hash值
		$key = crc32(implode(':', $driver));
		// 是否已经创建过单例对象
		empty(self::$instance[$key]) and (self::$instance[$key] = new self($driver));
		// 返回对象
		return self::$instance[$key];
	}

	/**
	 * 执行sql语句
	 * @param string sql语句
	 * @param array 参数数组
	 * @return void
	 */
	public function query($sql, $params = array())
	{
		// 预处理语句
		$this->stmt = $this->pdo->prepare($sql);
		// 参数绑定
		$params and $this->bindValue($params);
		// sql语句执行
		return $this->stmt->execute();
	}

	/**
	 * 参数与数据类型绑定
	 * @param array 预处理值数组
	 * @return void
	 */
	private function bindValue($params)
	{
		foreach ($params as $key => $value)
		{
			switch (TRUE)
			{
				case is_numeric($value):
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
	 * @param string 函数名
	 * @param array 参数数组
	 * @return mixed
	 */
	public function __call($method, $args)
	{
		switch ($method)
		{
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
				$this->stmt->setFetchMode(\PDO::FETCH_ASSOC);
			case 'rowCount':
				$result = $this->stmt->$method();
				break;
			default:
				throw new \PDOException("Call to undefined method Mysql::{$method}()");
		}
		// 删除结果集
		$this->resetStmt();
		// 返回结果
		return $result;
	}

	/**
	 * 清空stmt对象
	 * @return void
	 */
	protected function resetStmt()
	{
		$this->stmt = NULL;
	}
}

/**
 * 使用说明:
 * 1. 配置说明: $driver = ['host'=>'127.0.0.1', port=>3306, dbname=>'test', 'charset'=>'utf8', 'username'=>'root', 'password'=>123456];
 * 2. 获取对象: $mysql = Mysql::getInstance($driver);
 * 3. 函数说明:
 * 3.1 执行sql语句: $mysql->query(string $sql, array $values=array());
 * 3.2 关于事务的函数
 * 3.2.1 开启事务: $mysql->beginTransaction();
 * 3.2.2 提交事务: $mysql->commit();
 * 3.2.3 回滚事务: $mysql->rollback();
 * 3.2.4 判断是否在一个事务中: $mysql->inTransaction();
 * 3.3 关于执行结果获取的函数
 * 3.3.1 获取上次插入的id: $mysql->lastInsertId();
 * 3.3.2 获取影响的行数: $mysql->rowCount();
 * 3.3.3 获取所有的查询结果: $mysql->fetchAll();
 * 3.3.4 获取一行查询结果: $mysql->fetch();
 * 3.3.5 获取一个查询结果的值: $mysql->fetchColumn();
 */