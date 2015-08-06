<?php
/**
 * Sql类
 */
namespace Driver;

class Sql extends Driver
{    
    /**
     * 解析所有
     * @var string
     */
    const FETCH_ALL = 'fetchAll';
    
    /**
     * 解析一行
     * @var string
     */
    const FETCH_ROW = 'fetch';
    
    /**
     * 解析一个值
     * @var string
     */
    const FETCH_ONE = 'fetchColumn';
    
    /**
     * 获取影响的行数
     * @var string
     */
    const RESULT_ROW = 'rowCount';
    
    /**
     * 获取上次插入的id
     * @var string
     */
    const RESULT_ID = "lastInsertId";

    /**
     * 附加的查询条件
     * @var array
     */
    protected $sql = array( 
        'field' => '*',
        'where' => NULL,
        'group' => NULL,
        'having' => NULL,
        'order' => NULL,
        'limit' => NULL,
        'prepare'=>NULL,
        'keys' => NULL,
        'values' => NULL
    );

    /**
     * 表名
     * @var string
     */
    protected $table;

    /**
     * 创建对象
     * @param string $table
     * @param array $config
     */
    protected function __construct(array $config)
    {
        $this->table = $config['table'];
        // 读取配置的操作
        $this->db = Mysql::getInstance($config);
    }

    /**
     * 条件回调方法, 设计原则,只字数最少的方式
     * @param string 方法名 field|where|group|having|order|limit|beginTransaction|inTransaction|commit|rollback
     * @param array 参数列表
     * @return \Mvc\Model
     */
    public final function __call($method, $args)
    {
        switch ($method)
        {
            case 'beginTransaction':
            case 'inTransaction':
            case 'commit':
            case 'rollback':
                // 事务
                $this->db->$method();
                break;
            default:
                throw new \Exception("Call to undefined method Model::{$method}()");
        }
        
        return $this;
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
    private final function comCondition($condition, $field, $return = FALSE)
    {
        static $interval = 0;
    
        $where = $data = array();
        foreach ($condition as $key => $option)
        {
            // false null array() "" 的时候全部过滤
            if (! $option && ! is_int($option))
            {
                continue;
            }
    
            if (is_array($option))
            {
                if ($lan = strpos($key, " b"))
                {
                    // between...and...
                    $key = trim(substr($key, 0, $lan));
                    $where[] = "{$key} BETWEEN :{$key}{$interval}_1 AND :{$key}{$interval}_2";
                    $this->sql['values'][":{$key}{$interval}_1"] = $option[0];
                    $this->sql['values'][":{$key}{$interval}_2"] = $option[1];
                }
                elseif (is_string(key($option)))
                {
                    // or
                    $or = array();
                    foreach ($option as $k => $o)
                    {
                        $o = array(
                            $k => $o
                        );
                        list($or[]) = $this->comCondition($o, $field, TRUE);
                    }
                    $where[] = "(" . implode(" OR ", $or) . ")";
                    continue;
                }
                else
                {
                    // in not in
                    $operation = strpos($key, " n") ? "NOT IN" : "IN";
                    $key = strpos($key, " n") ? trim(substr($key, 0, count($key) + 1)) : $key;
                    foreach ($option as $k => $val)
                    {
                        $temp[] = ":{$key}{$interval}_{$k}";
                        $this->sql['values'][":{$key}{$interval}_{$k}"] = $val;
                    }
                    $where[] = "{$key} {$operation}(" . implode(',', $temp) . ")";
                }
            }
            else if ($lan = strpos($key, " "))
            {
                // > >= < <= !=
                $subkey = substr($key, 0, $lan);
                $where[] = "{$key} :{$field}{$interval}";
                $this->sql['values'][":{$field}{$interval}"] = $option;
            }
            else
            {
                if ((strpos($option, "%") !== FALSE) || (strpos($option, '?') !== FALSE))
                {
                    // like
                    $where[] = "{$key} LIKE :{$field}{$interval}";
                    $this->sql['values'][":{$field}{$interval}"] = $option;
                }
                else
                {
                    // =
                    $where[] = "{$key}=:{$key}{$interval}";
                    $this->sql['values'][":{$key}{$interval}"] = $option;
                }
            }
            $interval ++;
        }
    
        if ($return)
        {
            return $where;
        }
        else
        {
            $this->sql[$field] = strtoupper($field) . " " . implode(' AND ', $where);
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
        $this->sql['group'] = "GROUP BY {$args}";    
        return $this;
    }
    
    /**
     * limit子句
     * @param int 偏移量或者个数
     * @param int 个数
     * @return Sql
     */
    public function limit($offset, $number = NULL)
    {
        if ($number)
        {
            // 偏移量和个数都存在
            $this->sql['values'][':limit_offset'] = $offset;
            $this->sql['values'][':limit_number'] = $number;
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
     * 拼接一条插入的sql语句
     * @param array 待插入的数据
     * @param const 返回类型
     * @return void
     */
    public function insert(array $data, $returnType=self::RESULT_ID)
    {
        // 数据整理
        $data = count($data) != count($data, COUNT_RECURSIVE) ? $data : array($data);
        // 设置插入的键
        $this->sql['keys'] = array_keys($data[0]);
        // 设置插入的值
        foreach ($data as $key => $insert)
        {
            $prepare = array();    
            foreach ($this->sql['keys'] as $prev)
            {
                $placeholder = ":{$prev}_{$key}"; // 占位符号    
                $prepare[] = $placeholder;    
                $this->sql['values'][$placeholder] = array_shift($insert);
            }    
            $this->sql['prepare'][] = sprintf("(%s)", implode(',', $prepare));
        }
        // 预处理sql语句
        $preKeys = sprintf("(%s)", implode(',', $this->sql['keys']));
        // 插入对应的预处理值
        $preValues = implode(',', $this->sql['prepare']);
        // 插入语句
        $sql = "INSERT INTO {$this->table}{$preKeys} VALUES {$preValues}";
        // 执行sql语句
        $this->db->query($sql, $this->sql['values']);
        // 结果返回
        return $returnType==self::RESULT_ID ? $this->db->lastInsertId() : $this->db->rowCount();
    }
    
    /**
     * 获取预处理删除语句
     * @return array sql语句,预处理值数组
     */
    public final function delete()
    {
        $sql = "DELETE FROM {$this->table} {$this->sql['where']} {$this->sql['limit']}";
        $this->db->query($sql, $this->sql['values']);
        return $this->db->rowCount();
    }
    
    /**
     * 拼接sql语句
     * @return string
     */
    public function select($returnType=self::FETCH_ALL)
    {
        // 拼接sql语句
        $sql = "SELECT {$this->sql['field']} FROM {$this->table} {$this->sql['where']} {$this->sql['group']} {$this->sql['having']} {$this->sql['order']} {$this->sql['limit']}";
        // 执行sql语句
        $this->db->query($sql, $this->sql['values']);
        // 返回类型
        switch($returnType)
        {
            case self::FETCH_ALL:
                return $this->db->fetchAll();
            case self::FETCH_ROW:
                return $this->db->fetch();
            case self::FETCH_ONE:
                return $this->db->fetchColumn();
        }
    }
    
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

    /**
     * 输出预绑定sql和参数列表
     * @param string 预处理sql语句
     * @param array 数据
     * @return void
     */
    public function debug($sql, $data)
    {
        foreach ($data as $key => $placeholder)
        {
            // 字符串加上引号
            is_string($placeholder) and ($placeholder = "'{$placeholder}'");
            // 替换
            $start = strpos($sql, $key);
            $end = strlen($key);
            $sql = substr_replace($sql, $placeholder, $start, $end);
        }
        
        exit($sql);
    }
}