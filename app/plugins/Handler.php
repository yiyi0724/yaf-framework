<?php

use \Yaf\Session;
use \Yaf\Application;
use \Yaf\Plugin_Abstract;
use \Yaf\Request_Abstract;
use \Yaf\Response_Abstract;

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
	private function initConst($request)
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
		define('MODULE', APPLICATION_PATH . "modules/{$request->getModuleName()}/");
		
		// URL常量定义
		$resources = Application::app()->getConfig()->get("resource.{$request->getModuleName()}");
		foreach($resources as $key=>$value)
		{
			define('RESOURCE_' . strtoupper($key), $value);
		}
		
		// 用户常量定义
		define('UID', Session::getInstance()->get('member.uid'));
	}

	/**
	 * 默认行为变更
	 */
	private function behavior()
	{
		// ajax自动关闭视图
		IS_AJAX AND Application::app()->getDispatcher()->disableView();
	}
	
	/**
	 * 参数整合
	 * @param unknown $request
	 */
	private function input($request)
	{
		$from = array();
		if(IS_PUT || IS_DELETE)
		{
			// PUT和DETELE方法支持
			parse_str(file_get_contents('php://input'), $from);
		}
		$from = array_merge($request->getParams(), $from, $_REQUEST);		
		$GLOBALS["_{$_SERVER['REQUEST_METHOD']}"] = $_REQUEST = $from;
	}
}