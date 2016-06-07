<?php

/**
 * 以/模块/控制器/方法的路由调用方式
 * @author enychen
 */
namespace traits;

use \Yaf\Application;
use \Yaf\Route_Interface;

class Route implements Route_Interface {

	/**
	 * 默认路由信息
	 * @var array
	 */
	protected $route = array(
		'module'=>'www',
		'controller'=>'index',
		'action'=>'index'
	);

	/**
	 * 已在Yaf中注册的模块
	 * @var array
	 */
	protected $modules = array();

	/**
	 * 模块处理信息
	 * @var array
	 */
	protected $moduleType;

	/**
	 * 构造函数，获取所有模块信息并删除默认模块
	 * @return void
	 */
	public function __construct() {
		// 模块信息
		$this->modules = Application::app()->getModules();
		unset($this->modules[array_search('Index', $this->modules)]);
		foreach($this->modules as $key=>$module) {
			$this->modules[$key] = strtolower($module);
		}
		// 路由分析信息
		$this->moduleType = Application::app()->getConfig()->get('application.route.type');
	}

	/**
	 * 路由调度
	 * @param \Yaf\Request\Http $request http请求对象
	 * @return boolean TRUE表示和其他路由协议共存
	 */
	public function route($request) {
		// 解析url信息
		$uri = $request->getRequestUri();
		$uri = explode('/', trim($request->getRequestUri(), '/'));
		$module = strtolower($uri[0]);
		
		// 二级域名还是path信息
		if($this->moduleType == 'domain') {
			$module = explode('.', $request->getServer('HTTP_HOST'));
			if(count($module) > 2) {
				$module = strtolower($module[0]);
			}
		} else {
			$module = strtolower($uri[0]);
		}

		if(in_array($module, $this->modules)) {
			$this->route['module'] = $module;
			array_splice($uri, 0, 1);
		}
		if(isset($uri[0])) {
			$this->route['controller'] = $uri[0];
		}
		if(isset($uri[1])) {
			$this->route['action'] = $uri[1];
		}

		$request->setModuleName($this->route['module']);
		$request->setControllerName(ucfirst($this->route['controller']));
		$request->setActionName(ucfirst($this->route['action']));

		return TRUE;
	}

	/**
	 * 不知道什么鬼东西，但是又必须继承
	 * @param array $info
	 * @param array $query
	 * @return void
	 */
	public function assemble(array $info, array $query = NULL) {
	}
}
