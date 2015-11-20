<?php

namespace Module;

use \Driver\Mysql as Mysql;
use \Driver\Redis as Redis;
use \Yaf\Application;

abstract class Module
{
	/**
	 * 配置对象
	 * @var int
	 */
	protected $config;
	
	public function __construct()
	{
		// 配置对象
		$this->Config = Application::App()->getConfig();
	}
	
    /**
     * 单例获取模型对象
     * @return \Driver\Sql;
     */
    protected function getMy5755DbMaster()
    {
    	static $mysql;
    	if(!$mysql)
    	{
    		try
    		{
    			$mysql = Mysql::getInstance($conf);
    		} catch(\PDOException $e) {
    			header('Status: 404 NOT FOUND');
    			exit;
    		}
    	}
    	
    	return $mysql;
    }
    
    /**
     * 获取platform数据库对象
     */
    protected function getPlatformDbMaster()
    {
    	static $mysql;
    	if(!$mysql)
    	{
    		try {
    			$mysql = Mysql::getInstance($conf);
    		} catch(\PDOException $e) {
    			header('Status: 404 NOT FOUND');
    			exit;
    		}
    	}
    	 
    	return $mysql;
    }
    
    /**
     * 获取redis
     */
    protected function getRedis()
    {
    	try {
    		$conf['host'] = '127.0.0.1';
    		$conf['port'] = 6379;
    		$conf['db'] = 1;
    		$conf['auth'] = 0;
    		$conf['timeout'] = 30;
    		return Redis::getInstance($conf);
    	}catch(\RedisException $e) {
    		header('Status: 404 NOT FOUND');
    		exit;
    	}
    }
    
    /**
     * 获取条件列表
     * @param array $source
     * @param string $from
     * @param string $to
     * @return array
     */
    public function getCondition($source, $from='id', $to='id')
    {
    	$condition = array();
		foreach($source as $key=>$val) {
			$condition[$to][] = $val[$from];
		}
		
		return $condition;
	}
}