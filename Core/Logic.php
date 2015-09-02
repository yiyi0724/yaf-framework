<?php

namespace Core;

use \Driver\Mysql as Model; // 引入数据库

class Logic
{
    /**
     * 单例获取模型对象
     * @return \Driver\Sql;
     */
    protected function getMy5755Db()
    {
        $conf['host'] = "127.0.0.1";
        $conf['port'] = "3306";
        $conf['dbname'] = "my5755";
        $conf['charset'] = "utf8";
        $conf['username'] = "root";
        $conf['password'] = "123456";
        return Model::getInstance($conf);
    }
    
    /**
     * 获取platform数据库对象
     */
    protected function getPlatformDb()
    {
        $conf['host'] = "127.0.0.1";
        $conf['port'] = "3306";
        $conf['dbname'] = "platform";
        $conf['charset'] = "utf8";
        $conf['username'] = "root";
        $conf['password'] = "123456";
        return Model::getInstance($conf);
    }
}