<?php

/**
 * mysql的insert操作类
 * @author enychen
 */
namespace Sql;

class Insert extends Sql
{
    /**
     * 拼接一条插入的sql语句
     * @param array 待插入的数据
     * @return void
     */
    public function prepare(array $data)
    {
        // 数据整理
        $data = $this->arrange($data);
        // 设置插入的键
        $this->setKeys($data);
        // 设置插入的值
        $values = $this->setValues($data);
        // 结果返回
        return array($this->concat(), $values);
    }
    
    /**
     * 拼接sql语句
     * @return string
     */
    protected function concat()
    {
        // 插入对应的key
        $keys = sprintf("(%s)", implode(',', $this->sql['keys']));
        // 插入对应的预处理值
        $values = implode(',', $this->sql['values']);
        // 返回插入语句
        return "INSERT INTO {$this->table}{$keys} VALUES {$values}";
    }
}