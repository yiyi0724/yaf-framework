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
    public function getModel($config = 'default')
    {
        $conf = $this->getConfig();
        
        return Model::getInstance($conf);
    }

    /**
     * 获取配置
     */
    protected function getConfig()
    {
        $conf['host'] = "127.0.0.1";
        $conf['port'] = "3306";
        $conf['dbname'] = "test";
        $conf['charset'] = "utf8";
        $conf['username'] = "root";
        $conf['password'] = "123456";
        
        return $conf;
    }
}