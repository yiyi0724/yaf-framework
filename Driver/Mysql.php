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
	 * @var object
	 */
	private $pdo;

	/**
	 * 预处理对象
	 * @var object
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
		$option = array(
				\PDO::ATTR_ERRMODE=>\PDO::ERRMODE_EXCEPTION,// 如果出现错误抛出错误警告
				\PDO::ATTR_ORACLE_NULLS=>\PDO::NULL_TO_STRING,// 把所有的NULL改成""
				\PDO::ATTR_TIMEOUT=>30 // 超时时间
		);			
		// 创建数据库驱动对象
		$this->pdo = new \Pdo($dsn, $driver['username'], $driver['password'], $option);
	}
	
	/**
	 * 禁止克隆对象
	 * @return void
	 */
	private final function __clone(){}
	
	/**
	 * 创建数据库连接池对象
	 * @param array 配置对象数组,包含的key有 host,port,dbname,charset,username,password
	 * @return \Driver\Mysql
	 */
	public static function getInstance(array $driver)
	{
	    // 计算hash值
	    $key = crc32(implode(':', $driver));	    
	    // 是否已经创建过单例对象
	    if(empty(self::$instance[$key]))
	    {
	        self::$instance[$key] = new self($driver);
	    }	    
		// 返回对象
		return self::$instance[$key];
	}
	
	/**
	 * 执行sql查询
	 * @param string sql语句
	 * @param array 参数数组
	 * @return void
	 */
	public function query($sql, $params=array())
	{
		// 预处理绑定语句
		$this->stmt = $this->pdo->prepare($sql);
		// 参数绑定
		$params AND $this->bindValue($params);
        // sql语句执行
		if($this->stmt->execute())
		{
		    // 成功执行
			$this->stmt->setFetchMode(\PDO::FETCH_ASSOC);	
		}
		else
		{
		    // 错误报错
		    $error = $this->stmt->errorInfo();
		    $this->initStmt();
			throw new \PDOException($error[2], $error[1]);
		}
	}
	
	/**
	 * 参数与数据类型绑定
	 * @param array 值绑定
	 * @return void
	 */
	private function bindValue($params)
	{
		foreach($params as $key=>$value)
		{
			switch(TRUE)
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
		switch($method)
		{
		    case 'beginTransaction':
		    case 'inTransaction':
		    case 'commit':
		    case 'rollback':
			case 'lastInsertId':
				$result = $this->pdo->$method();
				break;
			case 'rowCount':
			case 'fetchAll':
			case 'fetch':
			case 'fetchColumn':
				$result = $this->stmt->$method();
				break;
			default:
			    throw new \PDOException("Call to undefined method Mysql::{$method}()");
		}
		// 删除结果集
		$this->initStmt();
		// 返回结果
		return $result;	
	}
	
	/**
	 * 清空stmt对象
	 * @return void
	 */
	protected function initStmt()
	{
	    $this->stmt = NULL;
	}
}