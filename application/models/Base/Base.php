<?php

namespace Base;

use \Yaf\Application;
use \Yaf\Config\Ini;
use \Driver\Mysql;

abstract class BaseModel
{

	/**
	 * 读取配置文件
	 * @var array
	 */
	protected $driver = array();
	
	/**
	 * 数据库对象
	 * @var \Driver\Mysql
	 */
	protected $mysql = NULL;
	
	/**
	 * redis对象
	 * @var \Driver\Redis
	 */
	protected $redis = NULL;

	/**
	 * 构造函数,加载配置
	 */
	public function __construct($mysql = 'master', $redis = 'master')
	{
		// 读取配置文件
		$this->driver = new Ini(CONF_PATH . 'driver.ini', Application::app()->environ());
		$mysql and $this->mysql = Mysql::getInstance($this->driver->get("mysql.{$mysql}")->toArray());
	}

	/**
	 * 读取配置信息
	 * @param unknown $key
	 * @return unknown
	 */
	protected function getConfig($key)
	{
		$result = Application::app()->getConfig()->get($key);
		return is_string($result) ? $result : $result->toArray();
	}
}

