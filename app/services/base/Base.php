<?php

/**
 * 逻辑组件基类
 * @author enychen
 */
namespace services\base;

class Base {
	
	/**
	 * 获取session对象
	 * @return \Yaf\Session
	 */
	public final function getSession() {
		return Session::getInstance();
	}
	
	/**
	 * 读取配置信息
	 * @param array $key 键名
	 * @return string|object|NULL 返回配置信息
	 */
	public final function getConfig($key) {
		return Application::app()->getConfig()->get($key);
	}
	
	/**
	 * 加载ini配置文件
	 * @param string $ini 文件名，不需要包含.ini后缀
	 * @return \Yaf\Config\Ini ini对象
	 */
	public final function loadIni($ini) {
		return new Ini(CONF_PATH . "{$ini}.ini", \YAF\ENVIRON);
	}
	
	public function isLogin($url = '/login') {
	}
	
	public function isExists() {
		
	}
}