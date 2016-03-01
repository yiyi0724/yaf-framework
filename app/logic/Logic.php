<?php

namespace logic;

use \Yaf\Session;

abstract class Logic
{
	/**
	 * 对象池
	 * @var array
	 */
	protected static $instance;
	
	/**
	 * 禁止创建对象
	 */
	protected final function __construct()
	{
	}

	/**
	 * 禁止克隆对象
	 */
	protected final function __clone()
	{
	}
	
	/**
	 * 单例模式获取对象
	 * @return \logic\Logic
	 */
	public static function getInstance()
	{
		if(!static::$instance)
		{
			static::$instance = new static();
		}
		
		return static::$instance;
	}

	/**
	 * 获取session对象
	 */
	public function getSession()
	{
		return Session::getInstance();
	}
	
	/**
	 * 
	 */
	public function getConfig()
	{
		
	}
}