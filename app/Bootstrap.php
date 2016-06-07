<?php
/**
 * 初始化自定义框架
 * @author enychen
 */

use Yaf\Loader;
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
			foreach($runtime->toArray() as $prefix=>$suffix) {
				if(is_array($suffix)) {
					foreach($suffix as $option=>$value) {
						ini_set("{$prefix}.{$option}", $value);
					}
				} else {
					ini_set($prefix, $suffix);
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
		Loader::getInstance(rtrim(APP_PATH, '/'))->registerLocalNamespace('service');
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
		$router->addRoute('enyRouter', new \traits\Route());
		// 路由重写正则
		$router->addConfig(new Ini(CONF_PATH . 'route.ini'));
	}

	/**
	 * 自定义视图对象
	 * @param \Yaf\Dispatcher $dispatcher 分发对象
	 * @return void
	 */
	public function _initView(Dispatcher $dispatcher) {
		$dispatcher->setView(new \traits\ViewInterface());
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