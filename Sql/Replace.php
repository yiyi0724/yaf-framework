<?php

/**
 * mysql的delete操作类
 * @author enychen
 */
namespace Sql;

class Replace extends Sql
{
    /**
     * 预处理替换
     */
    public final function prepare(array $data)
    {
        // 数据整理
        $data = $this->arrange($data);
        // 设置插入的键
        $this->setKeys($data);
        // 设置插入的值
        $values = $this->setValues($data);
        // 结果返回
        return array( 
            $this->concat(),
            $values
        );
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
        // 返回sql语句
        return "REPLACE INTO {$this->table}{$keys} VALUES{$values}";
    }
}
