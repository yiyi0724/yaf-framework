<?php
use Yaf\Bootstrap_Abstract;
use Yaf\Application;
use Yaf\Loader;
use Yaf\Dispatcher;
use Yaf\Config\Ini;
class Bootstrap extends Bootstrap_Abstract
{
	/**
	 * 修改php.ini的默认配置
	 * @param Dispatcher $dispatcher
	 */
	public function _initRuntime(Dispatcher $dispatcher)
	{
		if($runtime = Application::app()->getConfig()->get('runtime'))
		{				
			foreach($runtime as $prefix=>$config)
			{				
				foreach($config as $key=>$value)
				{
					ini_set("{$prefix}.{$key}", $value);
				}
			}
		}
	}
	
	/**
	 * 自定义逻辑加载类
	 * @param Dispatcher $dispatcher
	 */
	public function _initLoader(Dispatcher $dispatcher)
	{
		Loader::getInstance(rtrim(APPLICATION_PATH, '/'))->registerLocalNamespace('logic');
	}

	/**
	 * 修改路由信息
	 * @param \Yaf\Dispatcher $dispatcher 分发对象
	 */
	public function _initRoute(Dispatcher $dispatcher)
	{
		// 路由对象
		$router = $dispatcher->getRouter();
		// 自定义路由协议
		$router->addRoute('enyRouter', new \Traits\Route());
		// 路由重写正则
		$routeConfig = new Ini(CONF_PATH . 'route.ini');
		$router->addConfig($routeConfig);
	}
	
	/**
	 * 注册插件
	 * @param \Yaf\Dispatcher $dispatcher 分发对象
	 */
	public function _initPlugin(Dispatcher $dispatcher)
	{
		// 行为插件
		$dispatcher->registerPlugin(new HandlerPlugin());
	}
}