<?php
namespace Mvc;

use \Driver\Mysql as Model;

class Controller
{
    /**
     * 数据库配置
     */
    
    /**
     * 单例获取模型对象
     * @return \Driver\Sql;
     */
    public function getMy5755Db($config = 'default')
    {
        $conf['host'] = "127.0.0.1";
        $conf['port'] = "3306";
        $conf['dbname'] = "my5755";
        $conf['charset'] = "utf8";
        $conf['username'] = "root";
        $conf['password'] = "123456";
        return Model::getInstance($conf);
    }
    
    public function getPlatformDb()
    {
        $conf['host'] = "127.0.0.1";
        $conf['port'] = "3306";
        $conf['dbname'] = "platform";
        $conf['charset'] = "utf8";
        $conf['username'] = "root";
        $conf['password'] = "123456";
        return Model::getInstance($conf);
    }
    
    /**
     * 分页
     */
    public function getList()
    {
        
    }
    
    /**
     * 获取指定的key数组
     */
    public function getFileds($result, $field)
    {
        $list = array();
        foreach($result as $value)
        {
            if(!in_array($value[$field], $list))
            {
                $list[] = $value[$field];
            }
        }
        
        return $list;
    }
}