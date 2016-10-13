<?php

/**
 * 模型基类（基于mysql数据库）
 * @author enychen
 */

use Yaf\Registry;
use \database\mysql\Driver;

abstract class AbstractModel {

    /**
     * 获取表操作对象
     * @param $table
     * @params string $adpter 适配器名称
     * @return \database\mysql\Table 表操作对象
     */
    protected final function T($table, $adapter = 'master') {
        $config = Registry::get('driverIni')->get("database.{$adapter}");
        $driver = Driver::getInstance($config->host, $config->port, $config->dbname,
            $config->charset, $config->username, $config->password);
        return $driver->table($table);
    }
}