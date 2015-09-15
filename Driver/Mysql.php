<?php
/**
 * mysql数据库类
 * @author enychen
 */
namespace Driver;

class Mysql extends Driver
{
    /**
     * 当前的pdo对象
     * @var \PDO
     */
    private $pdo;

    /**
     * 预处理对象
     * @var \PDOStatement
     */
    private $stmt;

    /**
     * 附加的查询条件
     * @var array
     */
    protected $sql = array(
        'field' => '*',
        'table' => NULL,
        'where' => NULL,
        'group' => NULL,
        'having' => NULL,
        'order' => NULL,
        'limit' => NULL,
        'prepare' => NULL,
        'keys' => NULL,
        'values' => array()
    );

    /**
     * 禁止直接new对象,保证单例模式
     * @param array 数组配置, host | port | dbname | charset | username | password
     * @return void
     */
    protected function __construct($driver)
    {
        // 数据库连接信息
        $dsn = "mysql:host={$driver['host']};port={$driver['port']};dbname={$driver['dbname']};charset={$driver['charset']}";

        // 驱动选项
        $options = array( 
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION, // 如果出现错误抛出错误警告
            \PDO::ATTR_TIMEOUT => 30 // 超时时间
        );

        // 创建数据库驱动对象
        $this->pdo = new \PDO($dsn, $driver['username'], $driver['password'], $options);
    }

    /**
     * 设置要查询的字段
     * @param string 查询字符串列表
     * @return \Driver\Mysql
     */
    public function field($field)
    {
        $this->sql['field'] = $field;
        return $this;
    }

    /**
     * 设置表名
     * @param string 表名
     * @return \Driver\Mysql
     */
    public function table($table)
    {
        $this->sql['table'] = $table;
        return $this;
    }

    /**
     * 拼接where子句
     * @return \Driver\Mysql
     */
    public function where($condition)
    {
        $condition = $this->comCondition($condition, 'where');
        $this->sql['where'] = "WHERE " . implode(' AND ', $condition);
        return $this;
    }

    /**
     * 拼接having子句
     * @return \Driver\Mysql
     */
    public function having($condition)
    {
        $condition = $this->comCondition($condition, 'having');
        $this->sql['having'] = "HAVING " . implode(' AND ', $condition);
        return $this;
    }

    /**
     * order子句
     * @return \Driver\Mysql
     */
    public function order($order)
    {
        $this->sql['order'] = "ORDER BY {$order}";
        return $this;
    }

    /**
     * group子句
     * @return \Driver\Mysql
     */
    public function group($group)
    {
        $this->sql['group'] = "GROUP BY {$group}";
        return $this;
    }

    /**
     * limit子句
     * @param int 偏移量
     * @param int 个数
     * @return \Driver\Mysql
     */
    public function limit($offset, $number)
    {
        $this->sql['values'][':limit_offset'] = $offset;
        $this->sql['values'][':limit_number'] = $number;
        $this->sql["limit"] = "LIMIT :limit_offset, :limit_number";
        return $this;
    }

    /**
     * 拼接条件子句
     * @param array 键值对数组
     * @param string where或者having
     * @return array
     */
    private final function comCondition($condition, $field)
    {
        if (is_string($condition))
        {
            return array(addslashes($condition));
        }
        
        static $interval = 0;
        
        $conds = array();
        foreach ($condition as $key => $value)
        {
            // false null array() "" 的时候全部过滤,0不过滤
            if (!$value && !is_numeric($value))
            {
                continue;
            }

            // 去掉两边的空格
            $key = trim($key);
            
            // 操作类型
            foreach(array(' B', ' NL', ' L', ' N', ' >', ' <', ' =', ' !', ' &', ' ^', ' |', NULL) as $from=>$action)
            {
                if($location=strpos($key, $action))
                {
                    $origin = $key;
                    $key = substr($key, 0, $location);
                    break;
                }
            }
            
            if($from==0) 
            {
                // between...and
                $conds[] = "{$key} BETWEEN :{$key}from{$interval} AND :{$key}to{$interval}";
                $this->sql['values'][":{$key}from{$interval}"] = $value[0];
                $this->sql['values'][":{$key}to{$interval}"] = $value[1];
            }
            else if($key == 'OR')
            {                
                // or
                $or = array();
                foreach ($value as $k => $o)
                {
                    $o = array($k => $o);
                    list($or[]) = $this->comCondition($o, $field);
                }
                $conds[] = "(".implode(" OR ", $or).")";
                continue;
            }
            else if(is_array($value))
            {
                // in | not in
                $expression = $from == 3 ? 'NOT IN' : 'IN';
                foreach ($value as $k => $val)
                {
                    $temp[] = ":{$key}{$interval}_{$k}";
                    $this->sql['values'][":{$key}{$interval}_{$k}"] = $val;
                }
                $conds[] = "{$key} {$expression}(" . implode(',', $temp) . ")";
            }
            else if (in_array($from, array(1, 2)))
            {
                // like
                $expression = $from == 2 ? 'LIKE' : 'NOT LIKE';
                $conds[] = "{$key} {$expression} :{$field}{$interval}";
                $this->sql['values'][":{$field}{$interval}"] = $value;
            }
            else if (in_array($from, array(4, 5, 6, 7, 8, 9, 10)))
            {
                // > >= < <= != & ^ |
                $conds[] = "{$origin} :{$key}{$interval}";
                $this->sql['values'][":{$key}{$interval}"] = $value;
            }
            else
            {
                // =
                $conds[] = "{$key}=:{$key}{$interval}";
                $this->sql['values'][":{$key}{$interval}"] = $value;
            }
            
            $interval ++;
        }
        
        return $conds;
    }

    /**
     * 执行插入
     * @param array 待插入的数据
     * @return \Driver\Mysql
     */
    public function insert(array $data)
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
        $sql = "INSERT INTO {$this->sql['table']}{$preKeys} VALUES {$preValues}";
        // 执行sql语句
        $this->query($sql, $this->sql['values']);
        // 结果返回
        return $this;
    }

    /**
     * 执行删除
     * @return \Driver\Mysql;
     */
    public final function delete()
    {
        // 拼接sql语句
        $sql = "DELETE FROM {$this->sql['table']} {$this->sql['where']} {$this->sql['limit']}";
        // 执行sql语句
        $this->query($sql, $this->sql['values']);
        // 返回结果
        return $this;
    }

    /**
     * 执行查询
     * @return \Driver\Mysql
     */
    public function select()
    {
        // 拼接sql语句
        $sql = "SELECT {$this->sql['field']} FROM {$this->sql['table']} {$this->sql['where']} {$this->sql['group']} {$this->sql['having']} {$this->sql['order']} {$this->sql['limit']}";
        // 执行sql语句
        $this->query($sql, $this->sql['values']);
        // 返回类型
        return $this;
    }

    /**
     * 执行更新
     * @param array 键值对数组
     * @return \Driver\Mysql;
     */
    public final function update(array $update)
    {
        foreach ($update as $key => $val)
        {
            // 自增等系列处理
            if (stripos($val, $key) !== FALSE)
            {
                foreach (array('+','-','*','/','^','&','|','!') as $opeartion)
                {
                    if (strpos($val, $opeartion))
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
        $sql = "UPDATE {$this->sql['table']} SET {$set} {$this->sql['where']} {$this->sql['order']} {$this->sql['limit']}";
        // 执行sql语句
        $this->query($sql, $this->sql['values']);
        // 返回当前对象
        return $this;
    }

    /**
     * 执行sql语句
     * @param string sql语句
     * @param array 值数组
     * @throws \PDOException
     */
    public function query($sql, $params = array())
    {
        if (defined('DEBUG_SQL'))
        {
            // 输出调试的sql语句
            $this->debug($sql, $params);
        }
        else
        {
            // 预处理语句
            $this->stmt = $this->pdo->prepare($sql);
            // 参数绑定
            $params and $this->bindValue($params);
            // sql语句执行
            $result = $this->stmt->execute();
            // 清空条件子句
            $this->resetSql();
            // 返回结果
            return $result;
        }
    }
    
    /**
     * 参数与数据类型绑定
     * @param array 预处理值数组
     * @return void
     */
    private function bindValue($params)
    {
        foreach ($params as $key => $value)
        {
            switch (TRUE)
            {
                case is_numeric($value):
                    $type = \PDO::PARAM_INT;
                    break;
                case is_bool($value):
                    $type = \PDO::PARAM_BOOL;
                    break;
                case is_null($value):
                    $type = \PDO::PARAM_NULL;
                    break;
                default:
                    $type = \PDO::PARAM_STR;
            }
            // 参数绑定
            $this->stmt->bindValue($key, $value, $type);
        }
    }
    
    /**
     * 简单回调pdo对象方法
     * @param string 函数名
     * @param array 参数数组
     * @return mixed
     */
    public function __call($method, $args)
    {
        switch ($method)
        {
            case 'beginTransaction':
            case 'inTransaction':
            case 'commit':
            case 'rollback':
            case 'lastInsertId':
                $result = $this->pdo->$method();
                break;
            case 'fetchAll':
            case 'fetch':
            case 'fetchColumn':
                $this->stmt->setFetchMode(\PDO::FETCH_ASSOC);
            case 'rowCount':
                $result = $this->stmt->$method();
                break;
            default:
                throw new \PDOException("Call to undefined method Mysql::{$method}()");
        }
        // 删除结果集
        $this->resetStmt();
        // 返回结果
        return $result;
    }
    
    /**
     * 清空stmt对象
     * @return void
     */
    protected function resetStmt()
    {
        $this->stmt = NULL;
    }

    /**
     * 重置条件查询
     * @return void
     */
    protected function resetSql()
    {
        $this->sql = array( 
            'field' => '*',
            'table' => NULL,
            'where' => NULL,
            'group' => NULL,
            'having' => NULL,
            'order' => NULL,
            'limit' => NULL,
            'prepare' => NULL,
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
        
        echo '<hr/> origin sql: ', $sql, '</pre>';
        exit;
    }
}