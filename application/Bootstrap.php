<?php

use Yaf\Bootstrap_Abstract;
use Yaf\Application;
use Yaf\Registry;
use Yaf\Session;

class Bootstrap extends Bootstrap_Abstract
{
	/**
	 * 注册全局变量
	 */
	public function _initRegistry()
	{
		// 全局注册配置对象
		$config = Application::App()->getConfig();
		Registry::set('config', $config);
				
		// 全局session对象
		$session = Session::getInstance();
		Registry::set('session', $session);
		$session->start();
	}
}