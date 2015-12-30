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
	 * 保存注册类
	 */
	public function _initRegistry(Dispatcher $dispatcher)
	{
		// 调度对象
		Registry::set('dispatcher', $dispatcher);
		// 请求对象
		Registry::set('request', $dispatcher->getRequest());
		// 配置对象
		Registry::set('config', Application::app()->getConfig());
	}
	
    /**
     * 通过派遣器得到默认的路由器
     * @param \Yaf\Dispatcher $dispatcher
     */
    public function _initRoute(Dispatcher $dispatcher)
    {
        $router = $dispatcher->getRouter();
        $routeConfig = new \Yaf\Config\Ini(CONF_PATH.'route.ini');
        $router->addConfig($routeConfig);
    }
}