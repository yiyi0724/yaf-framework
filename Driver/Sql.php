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
        // 设置表名
        $this->table = $config['table'];
        // 删除表名
        unset($config['table']);
        // 读取配置的操作
        $this->db = Mysql::getInstance($config);
    }
    
    /**
     * 开启事务
     * @return boolean
     */
    public function beginTransaction()
    {
        return $this->db->beginTransaction();
    }
    
    /**
     * 检查是否在一个事务内
     * @return boolean
     */
    public function inTransaction()
    {
        return $this->db->inTransaction();
    }
    
    /**
     * 提交一个事务
     * @return boolean
     */
    public function commit()
    {
        return $this->db->commit();
    }
    
    /**
     * 回滚一个事务
     * @return boolean
     */
    public function rollback()
    {
        return $this->db->rollback();
    }
    
    /**
     * 设置要查询的字段
     * @return \Driver\Sql;
     */
    public function field($args)
    {
        $this->sql['field'] = $args;
        return $this;
    }
    
    /**
     * 拼接where子句
     * @return \Driver\Sql;
     */
    public function where($args)
    {
        $this->comCondition($args, 'where');    
        return $this;
    }
    
    /**
     * 拼接having子句
     * @return \Driver\Sql;
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
                    $operation = strpos($key, " n") ? "NOT LIKE" : "LIKE";
                    $where[] = "{$key} {$operation} :{$field}{$interval}";
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
     * @return \Driver\Sql;
     */
    public function order($args)
    {
        $this->sql['order'] = "ORDER BY {$args}";    
        return $this;
    }
    
    /**
     * group子句
     * @return \Driver\Sql;
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
     * @return \Driver\Sql;
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
        $this->query($sql, $this->sql['values']);
        // 结果返回
        return $returnType==self::RESULT_ID ? $this->db->lastInsertId() : $this->db->rowCount();
    }
    
    /**
     * 获取预处理删除语句
     * @return array sql语句,预处理值数组
     */
    public final function delete()
    {
        // 拼接sql语句
        $sql = "DELETE FROM {$this->table} {$this->sql['where']} {$this->sql['limit']}";
        // 执行sql语句
        $this->query($sql, $this->sql['values']);
        // 返回结果
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
        $this->query($sql, $this->sql['values']);
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
                $set[] = "{$key}={$temp[0]}{$opeartion}:{$key}";
                $this->sql['values'][":{$key}"] = $temp[1];
            }
            else
            {
                // 普通赋值
                $set[] = "{$key}=:{$key}";
                $this->sql['values'][":{$key}"] = $val;
            }
        }
        // set语句
        $set = implode(',', $set);
        // sql语句
        $sql = "UPDATE {$this->table} SET {$set} {$this->sql['where']} {$this->sql['order']} {$this->sql['limit']}";
        // 执行sql语句
        $this->query($sql, $this->sql['values']);
        // 返回影响行数
        return $this->db->rowCount();
    }
    
    /**
     * 执行sql语句
     * @param unknown $sql
     * @throws \PDOException
     */
    public function query($sql, $data)
    {
        if(defined('DEBUG_SQL'))
        {
            // 输出调试的sql语句
            $this->debug($sql, $data);
        }
        else if(!$this->db->query($sql, $data))
        {
            // 报错
            throw new \PDOException('Could not execute sql');
        }
        else
        {
            // 清空条件子句
            $this->resetSql();
        }
    }
    
    /**
     * 重置条件查询
     * @return void
     */
    protected function resetSql()
    {
        $this->sql = array( 
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
    }

    /**
     * 输出预绑定sql和参数列表
     * @param string 预处理sql语句
     * @param array 数据
     * @return void
     */
    public function debug($sql, $data)
    {
        echo '<pre>';
        echo "placeholder sql: $sql<br/>";
        print_r($data);
        
        foreach ($data as $key => $placeholder)
        {
            // 字符串加上引号
            is_string($placeholder) and ($placeholder = "'{$placeholder}'");
            // 替换
            $start = strpos($sql, $key);
            $end = strlen($key);
            $sql = substr_replace($sql, $placeholder, $start, $end);
        }
        
        echo '<hr/> origin sql: ',$sql,'</pre>';
        exit;
    }
}


/**
 * 使用说明:
 * 1. 配置说明: $driver = ['host'=>'127.0.0.1', port=>3306, dbname=>'test', 'charset'=>'utf8', 'username'=>'root', 'password'=>123456, 'table'=>'tableName'];
 * 2. 单例获取表对象: $model = \Driver\Sql::getInstance($driver);
 * 3. 函数说明:
 * 3.0 连贯操作: $model->field()->where()->group()->order()->limit()->method();
 * 3.0.1 filed函数, 一般用于select中,输入要查询的字符串, 如 'a,b,c' 或者用 'count(a)'也是支持的
 * 3.0.2 where函数,输入一个数组
 * 3.0.2.1 ['a'=>1]      ===> a = 1
 * 3.0.2.2 ['a'=>[1,2]] ===> a in (1, 2)
 * 3.0.2.3 ['a n'=>[1,2]]  ===> a not in (1, 2);
 * 3.0.2.4 ['a b'=>[1,2]]  ===> a between 1 and 2
 * 3.0.2.5 ['a >'=>1] ===> a > 1, 其他符号类似,记住空格不能少
 * 3.0.2.6 ['a'=>'%a%'] ===> a like '%a%', 同理 ['a n'=>'?a?'] ===> a not like '?a?'
 * 3.0.2.7 [['a'=>1, 'b'=>'2']] ===> a = 1 or b = 2
 * 3.1 插入: $model->insert(array('columnName'=>'value'), 返回值|返回影响结果);
 * 3.1.1 第二个参数说明: RESULT_ID | RESULT_ROW
 * 3.2 删除: $model->delete(); 执行delete之前可以先用连贯操作限制条件
 * 3.3 更新: $model->update(); 执行update之前可以先用连贯操作限制条件
 * 3.4 查询: $model->field()->where()->group()->having()->order()->limit()->select();
 * 3.4.1 select()方法参数: FETCH_ALL | FETCH_ROW | FETCH_ONE
 * 3.5 原生sql使用: $model->query($sql, $data);
 * 3.6 输出调试语句, 定义一个常量 define('DEBUG_SQL', TRUE);则不执行,只输出sql语句
 */