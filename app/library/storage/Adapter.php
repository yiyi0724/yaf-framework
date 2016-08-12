<?php

/**
 * 存储基类
 * @author enychen
 */
namespace storage;

abstract class Adapter {

	/**
	 * 设置参数并设置过期时间
	 * @param string $key 键名
	 * @param string $value 值
	 * @param int $expire 过期时间
	 */
	abstract public function setWithExpire($key, $value, $expire = 0);

	/**
	 * 获取并且检查过期时间
	 * @param string $key 键名
	 * @param int $expire 过期时间
	 */
	abstract public function getWithExpire($key, $expire = 0);

	/**
	 * 禁止克隆
	 * @return void
	 */
	protected final function __clone() {
	}
}