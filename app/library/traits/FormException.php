<?php

/**
 * 表单异常
 * @author enychen
 * @version 1.0
 */
namespace traits;

class FormException extends \Exception {

	/**
	 * 表单异常
	 * @param array $message 表单异常数组
	 * @param number $code 表单异常码
	 * @param Exception $previous 表单异常链
	 */
	public function __construct($message = NULL, $code = 0, Exception $previous = NULL) {
		parent::__construct(json_encode($message), $code, $previous);
	}

	/**
	 * 重写获取异常信息
	 * return array 错误数组
	 */
	public function getError() {
		return json_decode(parent::getMessage(), TRUE);
	}
}