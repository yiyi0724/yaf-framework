<?php

/**
 * mysql数据库类
 * @author enychen
 * @version 1.0
 */
namespace Driver;

class Mysql
{
	/**
	 * 加载单例模式
	 * @var \Traits\Singleton
	 */
	use \Traits\Singleton;

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
	 * 创建PDO对象
	 * @param array $driver 数组配置, host | port | dbname | charset | username | password
	 * @throws \PDOException
	 */
	protected function create($driver)
	{
		// 数据库连接信息
		$dsn = "mysql:host={$driver['host']};port={$driver['port']};dbname={$driver['dbname']};charset={$driver['charset']}";
		
		// 驱动选项
		$options = array(
			\PDO::ATTR_ERRMODE=>\PDO::ERRMODE_EXCEPTION, \PDO::ATTR_TIMEOUT=>30
		);
		
		// 创建数据库驱动对象
		$this->pdo = new \PDO($dsn, $driver['username'], $driver['password'], $options);
	}

	/**
	 * 执行sql语句
	 * @param string $sql sql语句
	 * @param array $params 参数
	 * @throws \PDOException
	 */
	public function query($sql, array $params = array())
	{
		if(defined('DEBUG_SQL'))
		{
			// 输出调试的sql语句
			echo "<pre>placeholder sql: {$sql}<hr/>";
			print_r($params);
			foreach($params as $key=>$placeholder)
			{
				// 字符串加上引号
				is_string($placeholder) and ($placeholder = "'{$placeholder}'");
				$start = strpos($sql, $key);
				$end = strlen($key);
				$sql = substr_replace($sql, $placeholder, $start, $end);
			}
			exit("<hr/> origin sql: {$sql}</pre>");
		}
		
		// 预处理语句
		$this->stmt = $this->pdo->prepare($sql);
		// 参数绑定
		$this->bindValue($params);
		// sql语句执行
		$this->stmt->execute();
		// 结果解析成数组
		$this->stmt->setFetchMode(\PDO::FETCH_ASSOC);
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
			// 数据类型选择
			switch(TRUE)
			{
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
			case 'fetchAll':
			case 'fetch':
			case 'fetchColumn':				
			case 'rowCount':
				$result = $this->stmt->$method();
				break;
			default:
				throw new \PDOException("Call to undefined method Mysql::{$method}()");
		}
		
		// 返回结果
		return $result;
	}
}

/**
 * 使用说明(按照下面步骤即可):
 * 1. 获取某一个数据库的对象: $mysql = \Driver\Mysql::getInstance($config);  // $config数组包含: host port dbname charset username password 6个key,必须都有
 * 2. 内置函数
 * 2.1 开启事务: $mysql->beginTransaction();
 * 2.2 检查是否在一个事务内: $mysql->inTransaction();
 * 2.3 事务回滚: $mysql->rollback();
 * 2.4 事务提交: $mysql->commit();
 * 2.5 获得上次插入的id: $mysql->lastInsertId();
 * 2.6 从结果集中获取所有内容: $mysql->fetchAll();
 * 2.7 从结果集中获取一行内容: $mysql->fetch();
 * 2.8 从结果集中获取一个内容: $mysql->fetchColumn();
 * 2.9 获得影响行数: $mysql->rowCount();
 * 
 * 3. 连贯操作函数:
 * 3.1 $mysql->field('用,隔开的字符串')->table('表名')->where('数组或者字符串')->group('字符串')->order('字符串')->having('同where')->limit('偏移量', '个数')
 * 3.2 where和having函数的使用说明:
 * 3.2.1 如果传入的是字符串,则直接拼字符串
 * 3.2.2 数组说明:
 * 3.2.2.1 ['id'=>1] 拼接成 id = 1
 * 3.2.2.2 ['id >'=>1] 拼接成 id > 1, 同理其他比较运算符一致
 * 3.2.2.3 ['id'=>[1,2,3]] 拼接成 id IN(1,2,3), 同理['id N'=>[1,2,3]] 拼接成 id NOT IN(1,2,3)
 * 3.2.2.4 ['id B'=>[1,5]] 拼接成 id BETWEEN 1 AND 5
 * 3.2.2.5 ['OR'=>['id'=>1, 'other'=>1, ['other2'=>2, 'other3'=>3]]] 拼接成 id = 1 OR other = 1 OR other2 = 2 AND other3 = 3
 * 3.2.2.6 ['id L'=>'%chen%'] 拼接成 id LIKE '%chen%' 同理['id NL'=>'%chen%'] 拼接成 id NOT LIKE '%chen%'
 * 
 * 4. 连贯操作函数2,可配置上面的函数一起使用
 * 4.1 $mysql->select()->fetch();  进行select,select不是结尾,以fetch | fetchAll | fetchColumn进行的结尾
 * 4.2 $mysql->insert(array $data, $rowCount); 进行insert,第二个参数用于表示多个插入的时候,返回影响行数的操作
 * 4.3 $mysql->update(); 进行update
 * 4.4 $mysql->delete(); 进行delete
 *  
 *  5. 原生sql操作
 *  5.1 $mysql->query($sql, $params);
 *  
 *  6. 调试
 *  6.1 define('DEBUG_SQL', TRUE); 后,不执行sql语句,输出预处理sql语句，值数组，可执行的完整sql语句
 */