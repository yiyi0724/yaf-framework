<?php

use \Yaf\Application;
use \Yaf\Plugin_Abstract;
use \Yaf\Request_Abstract;
use \Yaf\Response_Abstract;
use \Yaf\Registry;

/**
 * 行为插件
 * @author enychen
 */
class HandlerPlugin extends Plugin_Abstract
{
	/**
	 * 进行路由分发
	 * @param Request_Abstract $request
	 * @param Response_Abstract $response
	 */
	public function preDispatch(Request_Abstract $request, Response_Abstract $response)
	{
		// 常量注册
		$this->initConst($request);		
		// 默认行为变更
		$this->behavior();
		// 输入数据整合
		$this->input($request);
	}
	
	/**
	 * 常量注册
	 * @param Request_Abstract $request
	 */
	private function initConst(\Yaf\Request\Http $request)
	{
		// 请求方式定义
		define('IS_AJAX', $request->isXmlHttpRequest());
		define('IS_GET', $request->isGet());
		define('IS_POST', $request->isPost());
		define('IS_PUT', $request->isPut());
		define('IS_DELETE', $_SERVER['REQUEST_METHOD'] == 'DELETE');
		
		// 模块常量定义
		define('CONTROLLER', $request->getControllerName());
		define('ACTION', $request->getActionName());
		define('MODULE', $request->getModuleName());
		define('MODULE_PATH', APPLICATION_PATH . "modules/{$request->getModuleName()}/");
		
		// URL常量定义
		if($resources = Application::app()->getConfig()->get("resource.{$request->getModuleName()}"))
		{
			foreach($resources as $key=>$value)
			{
				define('RESOURCE_' . strtoupper($key), $value);
			}
		}
	}

	/**
	 * 默认行为变更
	 */
	private function behavior()
	{
		// ajax自动关闭视图
		IS_AJAX AND Application::app()->getDispatcher()->disableView();
		// 默认错误和异常处理方式
		// 由于yaf默认把错误封装成了异常，所以只需要绑定一个方法就好
		set_exception_handler('\\Error\\'.MODULE.'Controller::shutdown');
	}
	
	/**
	 * 参数整合
	 * @param unknown $request
	 */
	private function input(Yaf\Request\Http $request)
	{
		$from = array();
		// PUT和DETELE方法支持
		if(IS_PUT || IS_DELETE)
		{
			parse_str(file_get_contents('php://input'), $from);
		}
		// 整合数据
		$from = array_merge($request->getParams(), $from, $_REQUEST);
		// 清空输入源
		$_GET = $_POST = $_REQUEST = array();
		// 整合到全局输入变量
		$GLOBALS['_INPUT'] = $from;
	}
}