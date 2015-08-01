<?php

namespace Sql;

abstract class Sql
{
    /**
     * 表名
     * @var <string>
     */
    protected $table;

    /**
     * 附加的查询条件
     * @var array
     */
    protected $sql = array(
        'field'=>'*',
        'where'=>NULL,
        'group'=>NULL,
        'having'=>NULL,
        'order'=>NULL,
        'limit'=>NULL,
    );

	/**
     * 构造函数
     * @param <string> 表名
     * @return <void>
     */
    public function __construct($table)
    {
        $this->table = $table;
    }
    
    /**
     * 设置字段
     */
    public function field($args)
    {
        $this->sql['field'] = $args;
    }

	/**
	 * where子句
	 */
	public function where($args)
	{
	    $this->comCondition($args, 'where');
	    
	    return $this;
	}

	/**
	 * having子句
	 */
	public function having($args)
	{
	    $this->comCondition($args, 'having');
	    
	    return $this;
	}

    /**
	 * 拼接条件子句
	 * @param array 键值对数组
	 * @return array 
	 */
	private final function comCondition($condition, $field, $return=FALSE)
	{
	    static $interval = 0;
	    
		$where = $data = array();
		foreach($condition as $key=>$option)
		{		    
			// false null array() "" 的时候全部过滤
			if(!$option && !is_int($option))
			{
				continue;
			}
			
			if(is_array($option))
			{
				if($lan = strpos($key, " b"))
				{
					// between...and...
					$key = trim(substr($key, 0, $lan));
					$where[] = "{$key} BETWEEN :{$key}_{$interval}_1 AND :{$key}_{$interval}_2";
					$this->sql['values'][":{$key}_{$interval}_1"] = $option[0];
					$this->sql['values'][":{$key}_{$interval}_2"] = $option[1];
				}
				elseif(is_string(key($option)))
				{
					// or
					$or = array();
					foreach($option as $k=>$o)
					{
						$o = array($k=>$o);
						list($or[]) = $this->comCondition($o, $field, TRUE);
					}
					$where[]  = "(".implode(" OR ", $or).")";
					continue;
				}
				else
				{
					// in not in
					$operation = strpos($key, " n") ? "NOT IN" : "IN";
					$key = strpos($key, " n") ? trim(substr($key, 0, count($key)+1)) : $key;
					foreach($option as $k=>$val)
					{
						$temp[] = ":{$key}_{$interval}_{$k}";
						$this->sql['values'][":{$key}_{$interval}_{$k}"] = $val;
					}
					$where[] = "{$key} {$operation}(".implode(',', $temp).")";
				}
			}
			else if($lan = strpos($key, " "))
			{
				// > >= < <= !=
				$subkey = substr($key, 0, $lan);
				$where[] = "{$key} :{$field}_{$interval}";
				$this->sql['values'][":{$field}_{$interval}"] = $option;
			}
			else if((strpos($option, "%") !== FALSE) || (strpos($option, '?') !== FALSE))
			{
				// like
				$where[] = "{$key} LIKE :{$field}_{$interval}";
				$this->sql['values'][":{$field}_{$interval}"] = $option;
			}
			else
			{
				// =
				$where[] = "{$key}=:{$key}_{$interval}";
				$this->sql['values'][":{$key}_{$interval}"] = $option;
			}
			$interval++;
		}
		
		if($return)
		{
			return $where;
		}
		else
		{
			$this->sql[$field] = strtoupper($field)." ".implode(' AND ', $where);
		}
	}

	/**
	 * order子句
	 * @return Sql
	 */
	public function order($args)
	{
	    $this->sql['order'] = "ORDER BY {$args}";
	    
	    return $this;
	}

	/**
	 * group子句
	 * @return Sql
	 */
	public function group($args)
	{
	    $this->sql['group'] ="GROUP BY {$args}";
	    
	    return $this;
	}

	/**
	 * limit子句
	 * @param int 偏移量或者个数
	 * @param int 个数
	 * @return Sql
	 */
	public function limit($offset, $number=NULL) 
	{
	    if($number)
	    {
	        // 偏移量和个数都存在
	        $this->values[':limit_offset'] = $offset;
	        $this->values[':limit_number'] = $number;
	        $this->sql["limit"] = "LIMIT :limit_offset, :limit_number";
	    }
	    else
	    {
	        // 没有偏移量只有个数
	        $this->values[':limit_number'] = $offset;
	        $this->sql["limit"] = "LIMIT :limit_number";
	    }
        
	    return $this;
	}
	
	/**
	 * 二维数组化,实现一条和多条数据插入
	 * @param array 输入信息
	 * @return void
	 */
	protected function arrange($data)
	{
	    return  count($data) != count($data, COUNT_RECURSIVE) ? $data : array($data);
	}
	
	/**
	 * 设置插入的对应建
	 * @return void
	 */
	protected function setKeys($data)
	{
	    $this->sql['keys'] = array_keys($data[0]);
	}
	
	/**
	 * 设置插入的值,使用预处理指令
	 * @param array 键数组
	 * @return void
	 */
	protected function setValues($data)
	{
	    $values = array();
	
	    foreach($data as $key=>$insert)
	    {
	        $prepare = array();
	
	        foreach($this->sql['keys'] as $prev)
	        {
	            $placeholder = ":{$prev}_{$key}"; // 占位符号
	
	            $prepare[] = $placeholder;
	
	            $values[$placeholder] = array_shift($insert);
	        }
	
	        $this->sql['values'][] = sprintf("(%s)", implode(',', $prepare));
	    }
	
	    return $values;
	}
}