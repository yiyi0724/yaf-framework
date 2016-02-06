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
	public function _initIni(Dispatcher $dispatcher)
	{
		if($inis = Application::app()->getConfig()->get('phpini'))
		{
			foreach($inis as $prefix=>$ini)
			{
				foreach($ini as $suffixe=>$value)
				{
					ini_set("{$prefix}.{$suffixe}", $value);
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
		$router = $dispatcher->getRouter();
		$routeConfig = new Ini(CONF_PATH . 'route.ini');
		$router->addConfig($routeConfig);
	}
}