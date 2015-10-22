<?php

namespace Core;

use \Driver\Mysql as Mysql;
use \Driver\Redis as Redis;

class Logic
{
    /**
     * 单例获取模型对象
     * @return \Driver\Sql;
     */
    protected function getMy5755DbMaster()
    {
        $conf['host'] = "127.0.0.1";
        $conf['port'] = "3306";
        $conf['dbname'] = "my5755";
        $conf['charset'] = "utf8";
        $conf['username'] = "root";
        $conf['password'] = "123456";
        return Mysql::getInstance($conf);
    }
    
    /**
     * 获取platform数据库对象
     */
    protected function getPlatformDbMaster()
    {
        $conf['host'] = "127.0.0.1";
        $conf['port'] = "3306";
        $conf['dbname'] = "platform";
        $conf['charset'] = "utf8";
        $conf['username'] = "root";
        $conf['password'] = "123456";
        return Mysql::getInstance($conf);
    }
    
    /**
     * 获取redis
     */
    protected function getRedis()
    {
        $conf['host'] = '127.0.0.1';
        $conf['port'] = 6379;
        $conf['db'] = 1;
        $conf['auth'] = 0;
        $conf['timeout'] = 30;
        return Redis::getInstance($conf);
    }
    
    /**
     * 把二维数组转换成一维数组
     * @param
     */
    public function merge(array $source, $implode=false, $key='id')
    {
    	$destination = array();

    	foreach($source as $key=>$val)
    	{
    		if(isset($val[$key]))
    		{
    			$destination[] = $val[$key];
    		}
    	}
    	
    	return $implode ? implode($implode, $news) : $news;
    }
}