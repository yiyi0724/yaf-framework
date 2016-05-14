<?php

/**
 * 模型基类
 * @author enychen
 */
namespace Base;

use \Yaf\Config\Ini;

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
	protected $db = NULL;

	/**
	 * 适配器
	 * @var string
	 */
	protected $adapter = 'master';
	
	/**
	 * 表名
	 * @var string
	 */
	protected $table = NULL;

	/**
	 * 构造函数,加载配置
	 * @return void
	 */
	public final function __construct() {
		$this->driver = new Ini(CONF_PATH . 'driver.ini', \YAF\ENVIRON);
		$this->db = $this->getMysql($this->adapter);
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
		$adapter = $this->driver['redis'][$adapter];
		return \Storage\Redis::getInstance($adapter->host, $adapter->port, $adapter->db, 
			$adapter->auth, $adapter->timeout, $adapter->options);
	}

	/**
	 * 分页获取信息
	 * @param int 			$page 	当前页
	 * @param int 			$number 每页几条
	 * @param array|string 	$where 	where条件
	 * @param string 		$order  order条件
	 * @param string 		$group 	group条件
	 * @param array|string  $having having条件
	 * @return array 分页信息
	 */
	public function paging($page = 1, $number = 15, $where = NULL, $order = NULL, $group = NULL, $having = NULL) {
		// 获取分页数量
		$this->db->field('COUNT(*)');
		$where and $this->db->where($where);
		$order and $this->db->order($order);
		$group and $this->db->group($group);
		$having and $this->db->having($having);
		$total = $this->db->select()->fetchColumn();
		
		// 获取本页数据
		$this->db->field('*');
		$this->db->limit(abs($page - 1) * $number, $number);
		$lists = $this->db->select()->fetchAll();
		
		// 输出分页
		$page = \Network\Page::showCenter($page, $number, $total);
		$page['lists'] = $lists;
		
		return $page;
	}
}