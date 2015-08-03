<?php
namespace Mvc;

class Controller
{

    /**
     * 模型池
     * @var unknown
     */
    public $modelMap;

    /**
     * 数据库配置
     */
    
    /**
     * 单例获取模型对象
     */
    public function getModel($table, $config = 'default')
    {
        $conf['host'] = "127.0.0.1";
        $conf['port'] = "3306";
        $conf['dbname'] = "test";
        $conf['charset'] = "utf8";
        $conf['username'] = "root";
        $conf['password'] = "123456";
        
        return new \Mvc\Model($table, $conf);
    }

    /**
     * 获取配置
     */
    protected function getConfig()
    {
    }
}