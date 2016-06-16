<?php

/**
 * 错误处理
 * @author enychen
 */
namespace traits;

class Error {

	/**
	 * 
	 * @param unknown $errno
	 * @param unknown $errstr
	 * @param unknown $errfile
	 * @param unknown $errline
	 * @return boolean
	 */
	public static function hander() {
		if($error = error_get_last()) {

		}
	}
}