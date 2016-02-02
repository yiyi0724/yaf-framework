<?php

use Yaf\Bootstrap_Abstract;
use Yaf\Application;
use Yaf\Dispatcher;
use Yaf\Config\Ini;

class Bootstrap extends Bootstrap_Abstract
{	
	/**
	 * 自定义逻辑加载类
	 * @param Dispatcher $dispatcher
	 */
	public function _initLoader(Dispatcher $dispatcher)
	{
		$loader = Loader::getInstance(rtrim(APPLICATION_PATH, '/'));
		$loader->registerLocalNamespace("logic");
	}
	
    /**
     * 修改路由信息
     * @param \Yaf\Dispatcher $dispatcher 分发对象
     */
    public function _initRoute(Dispatcher $dispatcher)
    {
        $router = $dispatcher->getRouter();
        $routeConfig = new Ini(CONF_PATH.'route.ini');
        $router->addConfig($routeConfig);
    }
}