<?php
/**
 * 初始化自定义框架
 * @author enychen
 */

use Yaf\Loader;
use \Traits\Route;
use Yaf\Dispatcher;
use Yaf\Config\Ini;
use Yaf\Application;
use Yaf\Bootstrap_Abstract;

class Bootstrap extends Bootstrap_Abstract {

	/**
	 * 修改php.ini的默认配置
	 * @param Yaf\Dispatcher $dispatcher 分发对象
	 * @return void
	 */
	public function _initRuntime(Dispatcher $dispatcher) {
		if($runtime = Application::app()->getConfig()->get('runtime')) {
			foreach($runtime as $prefix=>$suffix) {
				foreach($suffix as $key=>$value) {
					ini_set("{$prefix}.{$key}", $value);
				}
			}
		}
	}

	/**
	 * 自定义逻辑加载类
	 * @param Yaf\Dispatcher $dispatcher 分发对象
	 * @return void
	 */
	public function _initLoader(Dispatcher $dispatcher) {
		Loader::getInstance(rtrim(APPLICATION_PATH, '/'))->registerLocalNamespace('logic');
	}

	/**
	 * 修改路由信息
	 * @param \Yaf\Dispatcher $dispatcher 分发对象
	 * @return void
	 */
	public function _initRoute(Dispatcher $dispatcher) {
		// 路由对象
		$router = $dispatcher->getRouter();
		// 自定义路由协议
		$router->addRoute('enyRouter', new Route());
		// 路由重写正则
		$routeConfig = new Ini(CONF_PATH . 'route.ini');
		$router->addConfig($routeConfig);
	}

	/**
	 * 注册插件
	 * @param \Yaf\Dispatcher $dispatcher 分发对象
	 * @return void
	 */
	public function _initPlugin(Dispatcher $dispatcher) {
		$dispatcher->registerPlugin(new HandlerPlugin());
	}
}