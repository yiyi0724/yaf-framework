<?php

/**
 * 数据库驱动基类
 * @author enychen
 */
namespace database\driver;

abstract class Adapter {

	/**
	 * 是否进行调试
	 * @var bool
	 */
	protected $isDebug = FALSE;

	/**
	 * 设置是否进行调试
	 * @return void
	 */
	public function setIsDebug($isDebug) {
		$this->isDebug = (bool)$isDebug;
	}

	/**
	 * 获取是否进行调试
	 * @return boolean
	 */
	public function getIsDebug() {
		return $this->isDebug;
	}
	
	/**
	 * 禁止对象克隆
	 * @return void
	 */
	protected final function __clone() {
	}

	/**
	 * 单例获取数据库对象
	 * @param string $type	   	数据库类型，如mysql,sqlite
	 * @param string $host	  	数据库地址
	 * @param string $port		数据库端口
	 * @param string $charset	数据库字符集
	 * @param string $username 	数据库连接用户
	 * @param string $password 	数据库连接密码
	 * @return \database\driver\Adapter 具体某种驱动对象
	 */
	abstract public static function getInstance($type, $host, $port, $dbname, $charset, $username, $password);

	/**
	 * 执行sql语句
	 * @throws \PDOException
	 * @param string $sql	 sql语句
	 * @param array  $params 预绑定参数数组
	 * @return \database\driver\Adapter 具体某种驱动对象
	 */
	abstract public function query($sql, array $params = array());

	/**
	 * 调试sql语句
	 * @param string $sql	 sql语句
	 * @param array  $params 参数
	 * @return void
	 */
	abstract private function debug($sql, array $params = array());
}