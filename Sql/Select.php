<?php

/**
 * mysql的select操作类
 * @author enychen
 */
namespace Sql;

class Select extends Sql
{
    /**
     * 拼接一条查询的sql语句
     */
    public function prepare()
    {
        return array($this->concat(), $this->sql['values']);
    }
    
    /**
     * 拼接sql语句
     * @return string
     */
    protected function concat()
    {
        return "SELECT {$this->sql['field']} FROM {$this->table} {$this->sql['where']} {$this->sql['group']} {$this->sql['having']} {$this->sql['order']} {$this->sql['limit']}";
    }
}