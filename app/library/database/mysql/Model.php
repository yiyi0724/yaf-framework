<?php

/**
 * mysql模型
 * @author enychen
 * @version 1.0
 */
namespace database\mysql;


class Model {

    /**
     * 数据库驱动
     * @var Driver
     */
    protected $driver = NULL;

    /**
     * 表名
     * @var string
     */
    protected $table = NULL;

    /**
     * 查询条件列表
     * @var array
     */
    protected $sql = array(
        'field' => '*',
        'join' => NULL,
        'where' => NULL,
        'group' => NULL,
        'having' => NULL,
        'order' => NULL,
        'limit' => NULL,
        'lock' => NULL,
        'prepare' => NULL,
        'keys' => NULL,
        'values' => array()
    );

    /**
     * 构造函数
     * @param Driver $driver 数据库驱动
     * @param string $table 表名
     */
    public function __construct(\database\mysql\Driver $driver, $table) {
        $this->setDriver($driver)->setTable($table);
    }

    /**
     * 设置数据库驱动
     * @param Driver $driver 数据库驱动
     * @return $this
     */
    protected function setDriver($driver) {
        $this->driver = $driver;
        return $this;
    }

    /**
     * 获取数据库驱动
     * @return Driver
     */
    public function getDriver() {
        return $this->driver;
    }

    /**
     * 设置表名
     * @param string $table 表名
     * @return $this
     */
    protected function setTable($table) {
        $this->table = $table;
        return $this;
    }

    /**
     * 获取表名
     * @return string
     */
    public function getTable() {
        return $this->table;
    }

    /**
     * 设置要查询的字段
     * @param string $field 查询字符串列表
     * @return $this
     */
    public final function field($field) {
        $this->sql['field'] = $field;
        return $this;
    }

    /**
     * 拼接where子句
     *  格式为： where预处理语句, 值1, 值2, ...
     * @return $this
     */
    public final function where() {
        $result = $placeholder = array();
        $args = func_get_args();
        if (count($args) >= 0) {
            if (count($args) > 1) {
                preg_match_all('/\:[a-zA-Z][a-zA-Z0-9-_]*/', $args[0], $result);
                $result = $result[0];
                for ($i = 0, $len = count($result); $i < $len; $i++) {
                    $placeholder["{$result[$i]}"] = $args[$i + 1];
                }
            }
            $this->sql['where'] = "WHERE {$args[0]}";
            $this->sql['values'] = array_merge($this->sql['values'], $placeholder);
        }

        return $this;
    }

    /**
     * 拼接group子句
     * @param string $group 分组字符串
     * @return $this
     */
    public final function group($group) {
        $this->sql['group'] = "GROUP BY {$group}";
        return $this;
    }

    /**
     * 拼接order子句
     * @param string $order 排序字符串
     * @return $this
     */
    public final function order($order) {
        $this->sql['order'] = "ORDER BY {$order}";
        return $this;
    }

    /**
     * 拼接limit子句
     * @param int $offset 偏移量
     * @param int $limit 个数，不传表示偏移量为0
     * @return $this
     */
    public final function limit($offset, $limit = NULL) {
        if (!$limit) {
            $limit = $offset;
            $offset = 0;
        }
        $this->sql['values'][':limit_offset'] = $offset;
        $this->sql['values'][':limit_number'] = $limit;
        $this->sql["limit"] = "LIMIT :limit_offset, :limit_number";
        return $this;
    }

    /**
     * 拼接having子句
     *  格式为： having预处理语句, 值1, 值2, ...
     * @return $this
     */
    public final function having() {
        $result = $placeholder = array();
        $args = func_get_args();
        if (count($args) >= 0) {
            if (count($args) > 1) {
                preg_match_all('/\:[a-zA-Z][a-zA-Z0-9-_]*/', $args[0], $result);
                $result = $result[0];
                for ($i = 0, $len = count($result); $i < $len; $i++) {
                    $placeholder["{$result[$i]}"] = $args[$i + 1];
                }
            }
            $this->sql['having'] = "HAVING {$args[0]}";
            $this->sql['values'] = array_merge($this->sql['values'], $placeholder);
        }

        return $this;
    }

    /**
     * 执行悲观加锁
     *  在sql语句后面执行for update
     *  必须开启事务才有效果
     * @return $this
     */
    public final function lock() {
        $this->sql['lock'] = 'FOR UPDATE';
        return $this;
    }

    /**
     * 进行连接操作
     * @param string $table 要连接的表名
     * @param string $on 连接on条件
     * @param string $type LEFT|RIGHT|INNER 三种连接方式
     * @return $this
     */
    public final function join($table, $on, $type = 'INNER') {
        $this->sql['join'] = "{$type} JOIN {$table} ON {$on}";
        return $this;
    }

    /**
     * 执行插入
     * @param array $data 待插入的数据
     * @return Driver
     */
    public final function insert(array $data) {
        // 数据整理
        $data = count($data) != count($data, COUNT_RECURSIVE) ? $data : array($data);
        // 设置插入的键
        $this->sql['keys'] = array_keys($data[0]);
        // 设置插入的值
        foreach ($data as $key => $insert) {
            $prepare = array();
            foreach ($this->sql['keys'] as $prev) {
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
        $sql = "INSERT INTO {$this->getTable()}{$preKeys} VALUES {$preValues}";
        // 执行sql语句
        $this->query($sql, $this->sql['values']);
        // 清空数据
        $this->resetSql();
        // 结果返回
        return $this->getDriver();
    }

    /**
     * 执行删除
     * @return Driver
     */
    public final function delete() {
        // 拼接sql语句
        $sql = "DELETE FROM {$this->getTable()} {$this->sql['where']}{$this->sql['order']}{$this->sql['limit']}";
        // 执行sql语句
        $this->query($sql, $this->sql['values']);
        // 清空数据
        $this->resetSql();
        // 返回结果
        return $this->getDriver();
    }

    /**
     * 执行更新
     * @param array $update 键值对数组
     * @return Driver
     */
    public final function update(array $update) {
        foreach ($update as $key => $val) {
            if (preg_match('/([+|\-|\*|\/|%|&|\||\!|\^])(\d+)/', $val, $result)) {
                // 自增等系列处理
                $set = "`{$key}`=`{$key}`{$result[1]}:{$key}";
                $val = $result[2];
            } else {
                // 默认处理方式
                $set = "`{$key}`=:{$key}";
            }

            $sets[] = $set;
            $this->sql['values'][":{$key}"] = $val;
        }
        // set语句
        $sets = implode(',', $sets);
        // sql语句
        $sql = "UPDATE {$this->getTable()} SET {$sets} {$this->sql['where']} {$this->sql['order']} {$this->sql['limit']}";
        // 执行sql语句
        $this->query($sql, $this->sql['values']);
        // 清空数据
        $this->resetSql();
        // 返回当前对象
        return $this->getDriver();
    }

    /**
     * 执行查询,返回对象进行fetch操作
     * @param boolean $clear 是否清空条件信息，默认是
     * @return Driver
     */
    public final function select($clear = TRUE) {
        // 局部释放变量
        extract($this->sql);
        // 拼接sql语句
        $sql = "SELECT {$field} FROM {$this->getTable()} {$join} {$where} {$group} {$having} {$order} {$limit} {$lock}";
        // 执行sql语句
        $this->query($sql, $values);
        // 清空数据
        $clear and $this->resetSql();
        // 返回数据库操作对象
        return $this->getDriver();
    }

    /**
     * 执行原生sql语句
     * @param string $sql sql语句
     * @param array $params 参数
     * @return Driver
     */
    public function query($sql, array $params = array()) {
        $this->getDriver()->query($sql, $params);
        return $this->getDriver();
    }

    /**
     * 重置查询
     * @return void
     */
    protected final function resetSql() {
        $this->sql = array(
            'field' => '*',
            'join' => NULL,
            'where' => NULL,
            'group' => NULL,
            'having' => NULL,
            'order' => NULL,
            'limit' => NULL,
            'lock' => NULL,
            'prepare' => NULL,
            'keys' => NULL,
            'values' => array()
        );
    }
}