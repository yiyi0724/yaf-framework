<?php

/**
 * 数据库驱动基类
 * @author enychen
 */
namespace database;

abstract class Adapter {

	/**
	 * 是否进行调试
	 * @var bool
	 */
	protected $isDebug = FALSE;

	/**
	 * 设置是否进行调试
	 * @return void
	 */
	public function setIsDebug($isDebug) {
		$this->isDebug = (bool)$isDebug;
	}

	/**
	 * 获取是否进行调试
	 * @return boolean
	 */
	public function getIsDebug() {
		return $this->isDebug;
	}
	
	/**
	 * 禁止对象克隆
	 * @return void
	 */
	protected final function __clone() {
	}

	/**
	 * 执行sql语句
	 * @throws \PDOException
	 * @param string $sql	 sql语句
	 * @param array  $params 预绑定参数数组
	 * @return \database\driver\Adapter 具体某种驱动对象
	 */
	abstract public function query($sql, array $params = array());

	/**
	 * 调试sql语句
	 * @param string $sql	 sql语句
	 * @param array  $params 参数
	 * @return void
	 */
	abstract public function debug($sql, array $params = array());

	/**
	 * 开启事务
	 * @return boolean
	 */
	abstract public function beginTransaction();

	/**
	 * 判断是否在事务内
	 * @return boolean
	 */
	abstract public function inTransaction();

	/**
	 * 提交事务
	 * @return boolean
	 */
	abstract public function commitTransaction();

	/**
	 * 回滚事务
	 * @return boolean
	 */
	abstract public function rollbackTransaction();

	/**
	 * 获取上一次插入的id
	 * @return int
	 */
	abstract public function getLastInsertId();

	/**
	 * 获取影响的行数
	 * @return int
	 */
	abstract public function getAffectRowCount();

	/**
	 * 获取select的所有结果
	 * @return array
	 */
	abstract public function fetchAll();

	/**
	 * 获取select的一行结果
	 * @return array
	 */
	abstract public function fetchRow();

	/**
	 * 获取select的一个结果
	 * @return string
	 */
	abstract public function fetchOne();
}