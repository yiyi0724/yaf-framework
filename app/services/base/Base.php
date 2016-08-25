<?php

/**
 * 逻辑组件基类
 * @author enychen
 */
namespace services\base;

use \Yaf\Session;
use \traits\Request;
use \Yaf\Config\Ini;
use \Yaf\Application;

class Base {

	/**
	 * ini文件
	 * @var array
	 */
	protected static $inis = array();

	/**
	 * 获取session对象
	 * @return \Yaf\Session
	 */
	public final static function getSession() {
		return Session::getInstance();
	}

	/**
	 * 读取配置信息
	 * @param array $key 键名
	 * @return string|object|NULL 返回配置信息
	 */
	public final static function getConfig($key) {
		return Application::app()->getConfig()->get($key);
	}

	/**
	 * 加载ini配置文件
	 * @param string $ini 文件名，不需要包含.ini后缀
	 * @param string $key 配置键名
	 * @return mixed ini对象或者具体的值，找不到返回NULL
	 */
	public final static function getIni($ini, $key = NULL) {
		if(!self::$inis[$ini]) {
			self::$inis[$ini] = new Ini(sprintf("%s%s.ini", CONF_PATH, $ini), \YAF\ENVIRON);
		}
		return $key ? self::$inis[$ini]->get($key) : self::$inis[$ini];
	}

	/**
	 * 获取经过验证请求对象
	 * @return \traits\Request 请求封装对象
	 */
	public final static function getRequest() {
		return Request::getInstance();
	}

	/**
	 * 获取redis对象
	 * @param int $db 几号数据库，默认0
	 * @param string $adapter 适配器名称，默认master
	 * @return \storage\Redis redis的封装对象
	 */
	public final static function getRedis($db = 0, $adapter = 'master') {
		$c = $this->getIni('driver', sprintf("redis.%s", $adapter));
		$redis = \storage\Redis::getInstance($c->host, $c->port, $c->auth, $c->timeout, $c->options->toArray());
		$redis->select($db);
		return $redis;
	}
}