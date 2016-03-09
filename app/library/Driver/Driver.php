<?php

/**
 * 驱动基类
 * @author enychen
 * @version 1.0
 */
namespace Driver;

abstract class Driver {

	/**
	 * 对象池
	 * @var array
	 */
	protected static $pool;

	/**
	 * 禁止new对象，在此方法内实现连接操作
	 * @return void
	 */
	abstract protected function __construct(array $driver);

	/**
	 * 禁止克隆对象
	 * @return void
	 */
	protected final function __clone() {
	}

	/**
	 * 单例模式创建连接池对象
	 * @param array 数组配置
	 * @return \Drver\Driver
	 */
	public static function getInstance(array $driver) {
		// 拼接唯一key
		$key = "{$driver['host']}:{$driver['port']}:{$driver['dbname']}";
		
		// 是否已经创建过单例对象
		if(empty(static::$pool[$key])) {
			static::$pool[$key] = new static($driver);
		}
		
		// 返回对象
		return static::$pool[$key];
	}
}