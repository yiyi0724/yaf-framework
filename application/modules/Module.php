<?php

namespace Core;

use \Driver\Mysql as Mysql;
use \Driver\Redis as Redis;

class Module
{
    /**
     * 单例获取模型对象
     * @return \Driver\Sql;
     */
    protected function getMy5755DbMaster()
    {
    	try {
	        $conf['host'] = "127.0.0.1";
	        $conf['port'] = "3306";
	        $conf['dbname'] = "my5755";
	        $conf['charset'] = "utf8";
	        $conf['username'] = "root";
	        $conf['password'] = "123456";
	        return Mysql::getInstance($conf);
    	} catch(\PDOException $e) {
    		header('404 NOT FOUND');
    		exit;
    	}
    }
    
    /**
     * 获取platform数据库对象
     */
    protected function getPlatformDbMaster()
    {
    	try {
    		$conf['host'] = "127.0.0.1";
    		$conf['port'] = "3306";
    		$conf['dbname'] = "platform";
    		$conf['charset'] = "utf8";
    		$conf['username'] = "root";
    		$conf['password'] = "123456";
    		return Mysql::getInstance($conf);
    	} catch (\PDOException $e) {
    		header('404 NOT FOUND');
    		exit;
    	}
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
    		header('404 NOT FOUND');
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