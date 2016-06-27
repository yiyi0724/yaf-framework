<?php

/**
 * 模型基类
 * @author enychen
 */
namespace Base;

use \Yaf\Config\Ini;

abstract class AbstractModel {

	/**
	 * 组合mysql的方法
	 */
	use \database\traits\Mysql;
	
	/**
	 * 配置信息
	 * @var \Yaf\Config\Ini
	 */
	protected $config = NULL;

	/**
	 * 数据库对象
	 * @var \Database\Adapter
	 */
	protected $db = NULL;

	/**
	 * 配置适配器
	 * @var string
	 */
	protected $adapter = 'master';
	
	/**
	 * 驱动适配器
	 * @var string
	 */
	protected $driver = 'PDO';
	
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
		$this->config = new Ini(CONF_PATH . 'driver.ini', \YAF\ENVIRON);
	}
	
	/**
	 * 获取数据库驱动
	 * @return \Database\Adapter
	 */
	protected final function setDb() {
		$dbDriver = "\\Database\\{$this->driver}";
		$config = $this->config['database'][$this->adapter];
		return $dbDriver::getInstance($config->type, $config->host, $config->port,
			$config->dbname, $config->charset, $config->username, $config->password);
	}

	protected final function getDb() {
		return $this->db;
	}

	/**
	 * 获取redis
	 * @param string $adapter 适配器名称，默认master
	 * @return \Driver\Redis
	 */
	protected final function getRedis($adapter = 'master') {
		$config = $this->config['redis'][$adapter];
		return \Storage\Redis::getInstance($config->host, $config->port, $config->db, 
			$config->auth, $config->timeout, $config->options);
	}

	/**
	 * 执行原生sql语句
	 * @param string $sql sql语句
	 * @param array $params 参数
	 * @return
	 */
	public function query($sql, array $params = array()) {
		$this->setDb();
		return $this->getDb->query($sql, $params);
	}

	/**
	 * 分页获取信息
	 * @param int 			$page 	当前页
	 * @param int 			$number 每页几条
	 * @return array 分页信息
	 */
	public function paging($page = 1, $number = 15) {
		// 获取本页数据
		$this->limit(abs($page - 1) * $number, $number);
		$lists = $this->select()->fetchAll();
		
		// 获取分页数量
		$this->field('COUNT(*)');
		$total = $this->select(FALSE)->fetchColumn();
		
		// 输出分页
		$pagitor = \Network\Page::showCenter($page, $number, $total, 6);
		$pagitor['lists'] = $lists;
		
		return $pagitor;
	}
}