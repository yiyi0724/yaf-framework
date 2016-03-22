<?php

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
	 * @param \Yaf\Request_Abstract $request 请求对象
	 * @param \Yaf\Request_Abstract $response 响应对象
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
	 * @param Request_Abstract $request 请求对象
	 */
	private function initConst(Request_Abstract $request)
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
		if($resources = Application::app()->getConfig()->get('resource'))
		{			
			foreach($resources as $key=>$value)
			{
				define(strtoupper($key), $value);
			}
		}
	}

	/**
	 * 默认行为变更
	 */
	private function behavior()
	{
		// ajax关闭模板
		IS_AJAX and Application::app()->getDispatcher()->disableView();
	}
	
	/**
	 * 参数整合
	 * @param \Yaf\Request_Abstract $request 请求对象
	 */
	private function input(Request_Abstract $request)
	{
		$from = [];

		// PUT和DETELE方法支持
		if(IS_PUT || IS_DELETE)
		{
			parse_str(file_get_contents('php://input'), $from);
		}
		
		// 整合数据
		$inputs = array_merge($request->getParams(), $from, $_REQUEST);
		
		// 清空输入源
		$_GET = $_POST = $_REQUEST = [];
		
		// 整合到全局输入变量
		foreach($inputs as $key=>$input) {
			$request->setParam($key, $input);
		}
	}
}