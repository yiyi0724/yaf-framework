<?php

/**
 * 模型基类
 * @author enychen
 */
namespace Base;

use \Yaf\Session;
use \Yaf\Config\Ini;
use \Yaf\Application;

abstract class AbstractModel {

	/**
	 * 配置信息
	 * @var \Yaf\Config\Ini
	 */
	protected $driver = array();
	
	/**
	 * 数据库对象
	 * @var \Database\Mysql
	 */
	protected $mysql = NULL;

	/**
	 * 构造函数,加载配置
	 * @return void
	 */
	public final function __construct($adapter = 'master') {
		$this->driver = $this->loadIni('driver');
		$this->mysql = $this->getMysql($adapter);
	}

	/**
	 * 获取mysql对象
	 * @param string $adapter 适配器名称，默认master
	 * @return \Database\Mysql
	 */
	protected final function getMysql($adapter = 'master') {
		$adapter = $this->driver['mysql'][$adapter];
		return \Database\Mysql::getInstance($adapter->dsn, $adapter->username, $adapter->password);
	}

	/**
	 * 获取redis
	 * @param string $adapter 适配器名称，默认master
	 * @return \Driver\Redis
	 */
	protected final function getRedis($adapter = 'master') {
		return \Storage\Redis::getInstance($this->driver['redis'][$adapter]->toArray());
	}

	/**
	 * 分页获取信息
	 * @param int $page 当前页
	 * @param int $number 每页几条
	 * @param array|string $where where条件
	 * @param string $order 排序条件
	 * @param string $group 分组条件
	 * @param array|string having条件
	 */
	public function paging($page = 1, $number = 15) {
		// 获取分页数量
		$this->field('COUNT(*)');
		$count = $this->select()->fetchColumn();
		
		// 获取本页数据
		$this->db->field('*');
		$this->db->limit(($page - 1) * $number, $number);
		$lists = $this->select()->fetchAll();
		
		// 输出分页
		$page = \Network\Page::showCenter($page, $number, $count);
		$page['lists'] = $lists;
		
		return $page;
	}

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
	 * @return string|object
	 */
	public final function getConfig($key) {
		return Application::app()->getConfig()->get($key);
	}

	/**
	 * 加载ini配置文件
	 * @param string $ini 文件名，不包含.ini后缀
	 * @return \Yaf\Config\Ini
	 */
	public final function loadIni($ini) {
		return new Ini(CONF_PATH . "{$ini}.ini", \YAF\ENVIRON);
	}
}