<?php

namespace Sql;

class Update
{
    /**
     * 执行更新
     * @param array 键值对数组
     * @param boolean 是否输出调试语句
     * @return int 影响行数
     */
    public final function update(array $update)
    {
        foreach($update as $key=>$val)
        {
            // 自增等系列处理
            if(stripos($val, $key) !== FALSE)
            {
                foreach(array('+','-','*','/','^','&','|','!') as $opeartion)
                {
                    if(strpos($val, $opeartion))
                    {
                        $temp = explode($opeartion, $val);
                        break;
                    }
                }
                $set[] = "{$key}={$temp[0]}{$opeartion}:UPDATE{$key}";
                $this->values[":UPDATE{$key}"] = $temp[1];
            }
            else
            {
                // 普通赋值
                $set[] = "{$key}=:UPDATE{$key}";
                $this->values[":UPDATE{$key}"] = $val;
            }
        }
        // set语句
        $set = implode(',', $set);
        // 释放变量
        extract($this->condition);
        // sql语句
        $sql = "UPDATE {$this->table} SET {$set} {$where} {$order} {$limit}";
        // 执行更新
        $this->db->query($sql, $this->values);
        // 清空条件子句
        $this->setNull();
        // 返回影响行数
        return $this->db->affectRow();
    }
}