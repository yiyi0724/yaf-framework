<?php

use Yaf\Bootstrap_Abstract;
use Yaf\Dispatcher;
use Yaf\Application;
use Yaf\Registry;
use Yaf\Session;

class Bootstrap extends Bootstrap_Abstract
{	
	/**
	 * 定义输出头信息
	 */
	public function _initHeader(Dispatcher $dispatcher)
	{
		header('Content-Type:text/html;charset=UTF-8');
	}
	
	/**
	 * 更改路由
	 */
	public function _initRoute(Dispatcher $dispatcher)
	{

	}
}