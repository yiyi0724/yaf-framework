<?php

/**
 * 以/模块/控制器/方法的路由调用方式
 * @author enychen
 */
namespace Traits;

use \Yaf\Application;

class Route implements \Yaf\Route_Interface
{

	/**
	 * 默认路由信息
	 * @var array $route
	 */
	protected $route = array('module'=>'front', 'controller'=>'index', 'action'=>'index');

	/**
	 * 已在Yaf中注册的模块
	 * @var array $modules
	 */
	protected $modules = array();

	/**
	 * 构造函数，获取所有模块信息并删除默认模块
	 */
	public function __construct()
	{
		// 加载模块信息并且删除Index模块
		$this->modules = Application::app()->getModules();
		// 删除默认Index模块
		unset($this->modules[array_search('Index', $this->modules)]);
		// 模块小写匹配
		foreach($this->modules as $key=>$module)
		{
			$this->modules[$key] = strtolower($module);
		}
	}

	/**
	 * 路由调度
	 * @param  \Yaf\Request\Http  $request http请求对象
	 * @return boolean
	 */
	public function route($request)
	{
		// 获取url地址
		$uri = $request->getRequestUri();
		
		// 解析路由信息
		$uri = explode('/', trim($request->getRequestUri(), '/'));
		// 独立拿出模块
		$module = strtolower($uri[0]);
		
		// 模块修改
		if(in_array($module, $this->modules))
		{
			$this->route['module'] = $module;
			array_splice($uri, 0, 1);
		}
		// 控制器修改
		if(isset($uri[0]))
		{
			$this->route['controller'] = $uri[0];
		}
		// 方法修改
		if(isset($uri[1]))
		{
			$this->route['action'] = $uri[1];
		}
		// 更改调度信息
		$request->setModuleName($this->route['module']);
		$request->setControllerName(ucfirst($this->route['controller']));
		$request->setActionName(ucfirst($this->route['action']));
		
		return TRUE;
	}

	/**
	 * 不知道什么鬼东西
	 * @param array $info
	 * @param array $query
	 */
	public function assemble(array $info, array $query = NULL)
	{
	}
}
