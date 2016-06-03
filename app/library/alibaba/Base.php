<?php

/**
 * 阿里SDK基类
 * @author enychen
 */
namespace alisdk;

abstract class Base {

	/**
	 * 抛出异常信息
	 * @param int $code 异常码
	 * @param code $message 异常信息
	 * @throws Exception
	 */
	public function throws($code, $message) {
		throw new Exception($message, $code);
	}
}