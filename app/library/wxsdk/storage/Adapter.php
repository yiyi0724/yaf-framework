<?php

/**
 * 存储基类
 * @author enychen
 */
namespace wxsdk\storage;

abstract class Adapter {

	/**
	 * 对象池
	 * @var array
	 */
	protected static $pool;

	/**
	 * 设置参数并设置过期时间
	 * @param string $key 键名
	 * @param string $value 值
	 * @param int $expire 过期时间
	 * @return boolean
	 */
	abstract public function setWithExpire($key, $value, $expire = 0);

	/**
	 * 获取并且检查过期时间
	 * @param string $key 键名
	 * @return mixed
	 */
	abstract public function get($key);

	/**
	 * 禁止克隆
	 * @return void
	 */
	protected final function __clone() {
	}
}