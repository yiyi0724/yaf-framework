<?php

namespace Traits;

/**
 * 以/模块/控制器/方法的路由调用方式
 * @author enychen
 *
 */
class Route implements \Yaf\Route_Interface
{

	/**
	 * 已在Yaf中注册的模块
	 * @var array
	 */
	protected $modules = array();

	/**
	 * 构造函数，获取所有模块信息
	 */
	public function __construct()
	{
		// 加载模块信息并且删除Index模块
		$this->modules = \Yaf\Application::app()->getModules();
		unset($this->modules[array_search('Index', $this->modules)]);
		foreach($this->modules as $key=>$module)
		{
			$this->modules[$key] = strtolower($module);
		}
	}

	/**
	 *
	 * @param  \Yaf\Request\Http  $request
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
			// 具体模块
			$request->module = $module;
			//$request->setModuleName();
			array_splice($uri, 0);
		}
		else
		{
			// 默认模块
			$request->setModuleName('front');
		}		
		// 控制器修改
		if(isset($uri[0]))
		{
			$request->setControllerName($uri[0]);
		}		
		// 方法修改
		if(isset($uri[1]))
		{
			$request->setActionName($uri[1]);
		}	
		
		return false;
	}

	public function assemble(array $info, array $query = NULL)
	{
	}
}
