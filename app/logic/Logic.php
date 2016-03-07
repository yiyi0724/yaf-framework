<?php

/**
 * 逻辑层基类
 * @author enychen
 */
namespace logic;

use \Yaf\Session;
use \Yaf\Application;

abstract class Logic
{

	/**
	 * 获取session对象
	 * @return \Yaf\Session
	 */
	protected final function getSession()
	{
		return Session::getInstance();
	}

	/**
	 * 读取配置信息
	 * @param array $key 键名
	 * @return string|object
	 */
	protected final function getConfig($key)
	{
		return Application::app()->getConfig()->get($key);
	}
}