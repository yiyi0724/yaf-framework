<?php
/**
 * mysql数据库类
 * @author enychen
 */
namespace Driver;

class Mysql
{	
	/**
	 * 加载单例模式
	 * @var \Base\Traits
	 */
	use \Base\Singleton;
	
    /**
     * pdo对象
     * @var \Pdo
     */
    protected $pdo;

    /**
     * 预处理对象
     * @var \PDOStatement
     */
    protected $stmt;
    
    /**
     * 缓存对象
     * @var \Cache
     */
    protected $cache;
    
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
     * 迭代器
     * @var int
     */
    protected $interval = 0;

    /**
     * 创建PDO对象
     * @param array 数组配置, host | port | dbname | charset | username | password
     * @return void
     */
    protected function create($driver)
    {
        // 数据库连接信息
        $dsn = "mysql:host={$driver['host']};port={$driver['port']};dbname={$driver['dbname']};charset={$driver['charset']}";

        // 驱动选项
        $options = array(
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION, // 如果出现错误抛出异常
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
        $this->sql['where'] = 'WHERE ' . implode(' AND ', $condition);
        return $this;
    }

    /**
     * 拼接having子句
     * @return \Driver\Mysql
     */
    public function having($condition)
    {
        $condition = $this->comCondition($condition, 'having');
        $this->sql['having'] = 'HAVING ' . implode(' AND ', $condition);
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
    	// 字符串转义一下
        if (is_string($condition))
        {
            return array(addslashes($condition));
        }
        
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
            $operations = array(' B', ' NL', ' L', ' N', ' <>', ' >', ' <', ' !=', ' !', ' &', ' ^', ' |', NULL);
            foreach($operations as $from=>$action)
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
                $conds[] = "`{$key}` BETWEEN :{$key}from{$this->interval} AND :{$key}to{$this->interval}";
                $this->sql['values'][":{$key}from{$this->interval}"] = $value[0];
                $this->sql['values'][":{$key}to{$this->interval}"] = $value[1];
            }
            else if($key == 'OR')
            {                
                // or
                $or = array();
                foreach ($value as $orKey=>$orValue)
                {                    
                    $temp = is_array($orValue) ? $orValue : array($orKey => $orValue);
                    $temp = $this->comCondition($temp, $field);
                    $or[] = implode(' AND ', $temp);
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
                    $temp[] = ":`{$key}`{$this->interval}_{$k}";
                    $this->sql['values'][":{$key}{$this->interval}_{$k}"] = $val;
                }
                $conds[] = "`{$key}` {$expression}(" . implode(',', $temp) . ")";
            }
            else if (in_array($from, array(1, 2)))
            {
                // like
                $expression = $from == 2 ? 'LIKE' : 'NOT LIKE';
                $conds[] = "`{$key}` {$expression} :{$field}{$this->interval}";
                $this->sql['values'][":{$field}{$this->interval}"] = $value;
            }
            else if (in_array($from, array(4, 5, 6, 7, 8, 9, 10, 11)))
            {
                // > >= < <= != & ^ |
                $conds[] = "`{$key}`{$operations[$from]} :{$key}{$this->interval}";
                $this->sql['values'][":{$key}{$this->interval}"] = $value;
            }
            else
            {
                // =
                $conds[] = "`{$key}`=:{$key}{$this->interval}";
                $this->sql['values'][":{$key}{$this->interval}"] = $value;
            }
            
            $this->interval++;
        }
        
        return $conds;
    }

    /**
     * 执行插入
     * @param array 待插入的数据
     * @param bool 多行返回插入的行数
     * @return \Driver\Mysql
     */
    public function insert(array $data, $rowCount=FALSE)
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
        $preKeys = sprintf("(`%s`)", implode('`,`', $this->sql['keys']));
        // 插入对应的预处理值
        $preValues = implode(',', $this->sql['prepare']);
        // 插入语句
        $sql = "INSERT INTO {$this->sql['table']}{$preKeys} VALUES {$preValues}";
        // 执行sql语句
        $this->query($sql, $this->sql['values']);
        // 结果返回
        return $rowCount ? $this->rowCount() : $this->lastInsertId();
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
        return $this->rowCount();
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
                $set[] = "`{$key}`={$temp[0]}{$opeartion}:{$key}";
                $this->sql['values'][":{$key}"] = $temp[1];
            }
            else
            {
                // 普通赋值
                $set[] = "`{$key}`=:{$key}";
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
        return $this->rowCount();
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
        echo "placeholder sql: $sql<hr/>";
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

/**
 * 使用说明(按照下面步骤即可):
 * 1. 获取某一个数据库的对象: $mysql = \Driver\Mysql::getInstance($config);  // $config数组包含: host port dbname charset username password 6个key,必须都有
 * 2. 内置函数
 * 2.1 开启事务: $mysql->beginTransaction();
 * 2.2 检查是否在一个事务内: $mysql->inTransaction();
 * 2.3 事务回滚: $mysql->rollback();
 * 2.4 事务提交: $mysql->commit();
 * 2.5 获得上次插入的id: $mysql->lastInsertId();
 * 2.6 从结果集中获取所有内容: $mysql->fetchAll();
 * 2.7 从结果集中获取一行内容: $mysql->fetch();
 * 2.8 从结果集中获取一个内容: $mysql->fetchColumn();
 * 2.9 获得影响行数: $mysql->rowCount();
 * 
 * 3. 连贯操作函数:
 * 3.1 $mysql->field('用,隔开的字符串')->table('表名')->where('数组或者字符串')->group('字符串')->order('字符串')->having('同where')->limit('偏移量', '个数')
 * 3.2 where和having函数的使用说明:
 * 3.2.1 如果传入的是字符串,则直接拼字符串
 * 3.2.2 数组说明:
 * 3.2.2.1 ['id'=>1] 拼接成 id = 1
 * 3.2.2.2 ['id >'=>1] 拼接成 id > 1, 同理其他比较运算符一致
 * 3.2.2.3 ['id'=>[1,2,3]] 拼接成 id IN(1,2,3), 同理['id N'=>[1,2,3]] 拼接成 id NOT IN(1,2,3)
 * 3.2.2.4 ['id B'=>[1,5]] 拼接成 id BETWEEN 1 AND 5
 * 3.2.2.5 ['OR'=>['id'=>1, 'other'=>1, ['other2'=>2, 'other3'=>3]]] 拼接成 id = 1 OR other = 1 OR other2 = 2 AND other3 = 3
 * 3.2.2.6 ['id L'=>'%chen%'] 拼接成 id LIKE '%chen%' 同理['id NL'=>'%chen%'] 拼接成 id NOT LIKE '%chen%'
 * 
 * 4. 连贯操作函数2,可配置上面的函数一起使用
 * 4.1 $mysql->select()->fetch();  进行select,select不是结尾,以fetch | fetchAll | fetchColumn进行的结尾
 * 4.2 $mysql->insert(array $data, $rowCount); 进行insert,第二个参数用于表示多个插入的时候,返回影响行数的操作
 * 4.3 $mysql->update(); 进行update
 * 4.4 $mysql->delete(); 进行delete
 *  
 *  5. 原生sql操作
 *  5.1 $mysql->query($sql, $params);
 *  
 *  6. 调试
 *  6.1 define('DEBUG_SQL', TRUE); 后,不执行sql语句,输出预处理sql语句，值数组，可执行的完整sql语句
 */