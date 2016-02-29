<?php

namespace Error;

/**
 * 错误异常处理控制器
 * @author enychen
 */
class FrontController extends \Error\ErrorController
{
	/**
	 * 处理异常和错误的方法
	 */
	public static function shutdown($e)
	{
		echo '<pre>';
		print_r($e);exit;
		self::common($e);
	}
}