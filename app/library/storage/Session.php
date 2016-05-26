<?php

/**
 * session存储类
 * @author enychen
 * @version 1.0
 */
namespace Storage;

class Session {

	/**
	 * 禁止创建对象
	 * @return void
	 */
	protected final function __construct() {
	}
	
	/**
	 * 禁止克隆对象
	 * @return void
	 */
	protected final function __clone() {
		
	}
	
	/**
	 * 设置session
	 * @param string $key   键
	 * @param string $value 值
	 * @return mixed
	 */
	public static function set($key, $value) {
		$_SESSION[$key] = $value;
	}
	
	/**
	 * 获取session
	 * @param string $key	  键
	 * @param string $default 如果找不到返回此默认值
	 * @return mixed
	 */
	public static function get($key, $default = NULL) {
		$keys = explode('.', $key);
		foreach($keys as $key) {
			if(empty($_SESSION[$key])) {
				return $default;
			}
			$value = $_SESSION[$key];
		}
		
		return $value;
	}
	
	/**
	 * 删除session
	 * @param string $key 键
	 * @return void
	 */
	public static function delete($key) {
		unset($_SESSION[$key]);
	}
	
	/**
	 * 获取session并删除
	 * @param string $key	  键
	 * @param string $default 如果找不到返回此默认值
	 * @return mixed
	 */
	public static function getAndDelete($key, $default = NULL) {
		$value = static::get($key);
		static::delete($key);
		return $value;
	}
}