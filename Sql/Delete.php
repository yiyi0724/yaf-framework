<?php

/**
 * mysql的delete操作类
 * @author enychen
 */
namespace Sql;

class Delete extends Sql
{
    /**
     * 获取预处理删除语句
     * @return array sql语句,预处理值数组
     */
    public final function prepare()
    {
        return array( 
            $this->concat(),
            $this->sql['values']
        );
    }

    /**
     * 拼接sql语句
     * @return string
     */
    protected function concat()
    {
        return "DELETE FROM {$this->table} {$this->sql['where']} {$this->sql['limit']}";
    }
}