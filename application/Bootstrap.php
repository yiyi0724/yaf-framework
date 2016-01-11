<?php

use Yaf\Bootstrap_Abstract;
use Yaf\Dispatcher;
use Yaf\Config\Ini;

class Bootstrap extends Bootstrap_Abstract
{	
	public function _initConst()
	{
		
	}
	
    /**
     * 修改路由信息
     * @param \Yaf\Dispatcher $dispatcher
     */
    public function _initRoute(Dispatcher $dispatcher)
    {
        $router = $dispatcher->getRouter();
        $routeConfig = new Ini(CONF_PATH.'route.ini');
        $router->addConfig($routeConfig);
    }
}