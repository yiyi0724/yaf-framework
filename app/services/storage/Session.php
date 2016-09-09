<?php

namespace storage;

use \Yaf\Session;

class SessionService {

	/**
	 * 设置session
	 * @static
	 * @param string $key 键
	 * @param mixed $value 值
	 * @return void
	 */
	public static function set($key, $value) {
		Session::getInstance()->set($key, $value);
	}

	/**
	 * 获取session
	 * @static
	 * @param string $key 键
	 * @param mixed $default 获取不到的默认值，默认返回NULL
	 * @return mixed
	 */
	public static function get($key, $default = NULL) {
		$result = Session::getInstance()->get($key);
		return $result ? : $default;
	}

	/**
	 * 获取session并删除此session
	 * @static
	 * @param string $key 键
	 * @param mixed $default 获取不到的默认值，默认返回NULL
	 * @return mixed
	 */
	public static function getAndDel($key, $default = NULL) {
		$result = self::get($key);
		self::del($key);
		return $result ? : $default;
	}

	/**
	 * 删除session
	 * @static
	 * @return void
	 */
	public static function del() {
		$session = Session::getInstance();
		foreach(func_get_args() as $key) {
			$session->del($key);
		}
	}

	/**
	 * 清空session
	 * @static
	 * @return void
	 */
	public static function clear() {
		$session = Session::getInstance();
		foreach($_SESSION as $key=>$value) {
			$session->del($key);
		}
	}
}