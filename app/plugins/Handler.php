<?php

use \Yaf\Application;
use \Yaf\Plugin_Abstract;
use \Yaf\Request_Abstract;
use \Yaf\Response_Abstract;

/**
 * 行为插件
 * @author enychen
 */
class HandlerPlugin extends Plugin_Abstract {

	/**
	 * 进行路由分发
	 * @param \Yaf\Request_Abstract $request 请求对象
	 * @param \Yaf\Request_Abstract $response 响应对象
	 * @return void
	 */
	public function preDispatch(Request_Abstract $request, Response_Abstract $response) {
		// 常量注册
		$this->initConst($request);
	}

	/**
	 * 常量注册
	 * @param Request_Abstract $request 请求对象
	 * @return void
	 */
	private function initConst(Request_Abstract $request) {
		// 请求方式定义
		define('IS_AJAX', $request->isXmlHttpRequest());
		define('IS_GET', $request->isGet());
		define('IS_POST', $request->isPost());
		define('IS_PUT', $request->isPut());
		define('IS_DELETE', $_SERVER['REQUEST_METHOD'] == 'DELETE');
		
		// 模块信息常量定义
		define('CONTROLLER', $request->getControllerName());
		define('ACTION', $request->getActionName());
		define('MODULE', $request->getModuleName());
		define('MODULE_PATH', APPLICATION_PATH . "modules/{$request->getModuleName()}/");
		define('MODULE_VIEW', MODULE_PATH . 'views');
		define('FORM_FILE', MODULE_PATH . 'forms/' . strtolower(CONTROLLER) . '.php');
		
		// RESOURCE常量定义
		if($resources = Application::app()->getConfig()->get('resource')) {
			foreach($resources as $key=>$value) {
				define(strtoupper($key), $value);
			}
		}
	}
}