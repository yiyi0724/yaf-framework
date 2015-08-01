<?php

namespace Mvc;

use \Driver\Mysql;
use \Sql\Insert;
use \Sql\Delete;
use \Sql\Update;
use \Sql\Select;
use \Sql\Replace;

class Model
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
    const FECTH_ROW = 'fetch';

    /**
     * 解析一个值
     * @var string
     */
    const FECTH_ONE = 'fetchColumn';

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
     * 附加条件
     * @var array
     */
    protected $condition = array();

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
    public function __construct($table, $config)
    {
        $this->table = $table;
        $this->connect($config);
    }

    /**
     * 条件回调方法, 设计原则,只字数最少的方式
     * @param string 方法名 field|where|group|having|order|limit|beginTransaction|inTransaction|commit|rollback
     * @param array 参数列表
     * @return \Mvc\Model
     */
    public final function __call($method, $args)
    {
        switch($method)
        {
            case 'where':
            case 'having':
            case 'group':
            case 'order':
            case 'field':
            case 'limit':
                // 连贯操作
                $this->condition[$method] = $args;
                break;
            case 'beginTransaction':
            case 'inTransaction':
            case 'commit':
            case 'rollback':
                // 事务
                $this->db->$method();
                break;
            case 'insert':
            case 'delete':
            case 'update':
            case 'select':                
            case 'replace':
                return call_user_func_array(array($this, 'curd'), $this->setPrepare($method,$args));
            default:
                throw new \Exception("Call to undefined method Model::{$method}()");
        }

        return $this;
    }

    /**
     * 数据库连接
     */
    public function connect($config)
    {
        // 读取配置的操作
        $this->db = Mysql::getInstance($config);
    }
    
    /**
     * 增删改查替换操作
     */
    protected function curd($object, $data, $type=NULL)
    {
        // 设置查询条件
        $this->setCondition($object);
        // 预处理sql语句
        list($sql, $values) = $object->prepare($data);
        // 执行sql语句
        $this->db->query($sql, $values);
        // 清空附加条件
        $this->resetCondition();
        // 结果返回类型
        switch(TRUE)
        {
            case $object instanceof Insert:
            case $object instanceof Select:
            case $object instanceof Replace:
                return $this->db->$type();
            case $object instanceof Update:
            case $object instanceof Delete:
                // 返回影响行数
                return $this->db->rowCount();
        }
    }
    
    /**
     * 统计
     */
    public function count()
    {
    
    }
    
    /**
     * 求和
     */
    public function sum()
    {
    
    }
    
    /**
     * 输出预绑定sql和参数列表
     * @param string 预处理sql语句
     * @param array 数据
     * @return void
     */
    public function debug($sql, $data)
    {
        foreach($data as $key=>$placeholder)
        {
            // 字符串加上引号
            is_string($placeholder) AND ($placeholder = "'{$placeholder}'");
            // 替换
            $start = strpos($sql, $key);
            $end = strlen($key);
            $sql = substr_replace($sql, $placeholder, $start, $end);
        }

        exit($sql);
    }
    
    /**
     * 重置查询条件
     * @return void
     */
    public function resetCondition()
    {
        $this->condition = array();
    }
    
    /**
     * 原生sql执行
     */
    public function query($sql, $values)
    {
        return $this->db->query($sql, $values);
    }
    
    /**
     * 设置条件
     * @param \Sql $object
     */
    protected function setCondition($object)
    {
        // 设置附加条件
        foreach($this->condition as $method=>$condition)
        {
            call_user_func_array(array($object, $method), $condition);
        }
    }
    
    /**
     * 创建增删改查对象
     * @param string $method
     * @return \Sql\Insert|\ Sql\Delete|\Sql\Update|\Sql\Select|\Sql\Replace
     */
    protected function setPrepare($method, $args)
    {
        $data = array();
        switch($method)
        {
            case 'insert':
                $data[] = new Insert($this->table);
                $data[] = $args[0];
                $data[] = empty($args[1]) ? self::RESULT_ID : self::RESULT_ROW;
                break;            
            case 'delete':
                $data[] = new Delete($this->table);
                break;                
            case 'update':
                $data[] = new Update($this->table);
                break;
            case 'select':
                $data[] = new Select($this->table);
                $data[] = array();
                $data[] = isset($args[0]) ? $args[0] : self::FETCH_ALL;
                break;
                
            case 'replace':
                $data[] = new Replace($this->table);
        }
        
        return $data;
    }
}