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
		// 输入数据整合
		$this->inputFliter($request);
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
		define('FORM_FILE', MODULE_PATH . 'forms/' . strtolower(CONTROLLER) . '.php');
		
		// RESOURCE常量定义
		if($resources = Application::app()->getConfig()->get('resource')) {
			foreach($resources as $key=>$value) {
				define(strtoupper($key), $value);
			}
		}
	}

	/**
	 * 参数整合，清空全局变量
	 * @param \Yaf\Request_Abstract $request 请求对象
	 * @param array $putOrDelete put和delete方法支持存放数组
	 * @return void
	 */
	private function inputFliter(Request_Abstract $request, $putOrDelete = array()) {
		// PUT和DETELE方法支持
		if(IS_PUT || IS_DELETE) {
			parse_str(file_get_contents('php://input'), $putOrDelete);
		}
		
		// 输入数据源
		$params = array_merge($request->getParams(), $putOrDelete, $_REQUEST);
		
		// 获取检查规则
		if(is_file(FORM_FILE) && require (FORM_FILE)) {
			echo CONTROLLER . 'Controller::' . ACTION;exit;
			if(method_exists($formFile, ACTION . 'Action')) {
				$formLib = new \Security\Form(FORM_FILE);
				$rules = call_user_func(CONTROLLER . 'Controller::' . ACTION);
				\Security\Form::fliter($rules, $params);
			}
		}
	}
}