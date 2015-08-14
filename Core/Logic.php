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
    
    /**
     * 获取指定的key数组
     */
    protected function getFileds($result, $field)
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
    
    protected function auxiliary($origin, $auxiliary, $map)
    {
        foreach($auxiliary as $key=>$val)
        {
            foreach($origin as $k=>$v)
            {
                if($v[$map[0]] == $val[$map[1]])
                {
                    
                    $mybets[$k][] = $val['bet'];
                }
            }
        }
    }
}