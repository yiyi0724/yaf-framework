<?php


use \traits\View;
use \Yaf\Config\Ini;
use \Yaf\Dispatcher;
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
		// 输出初始化
		$this->initView($request);
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
		define('IS_DELETE', $request->getServer('REQUEST_METHOD') == 'DELETE');

		// 模块信息常量定义
		define('CONTROLLER_NAME', $request->getControllerName());
		define('ACTION_NAME', $request->getActionName());
		define('MODULE_NAME', $request->getModuleName());
		define('MODULE_PATH', sprintf("%smodules%s%s%s", APP_PATH, DS, $request->getModuleName(), DS));
		define('COMMON_VIEW_PATH', sprintf('%sviews%s', APP_PATH, DS));
		define('MODULE_VIEW_PATH', sprintf("%sviews%s", MODULE_PATH, DS));

		// RESOURCE常量定义
		$constIni = new Ini(CONF_PATH . 'consts.ini', \YAF\ENVIRON);
		foreach($constIni as $key=>$value) {
			define(strtoupper($key), $value);
		}
	}

	/**
	 * 常量注册
	 * @param Request_Abstract $request 请求对象
	 * @return void
	 */
	public function initView(Request_Abstract $request) {
		// 回调验证
		$callback = $request->get('callback');
		$callback = $callback && preg_match('/^[a-zA-Z_][a-zA-Z0-9_\.]*$/', $callback) ? $callback : NULL;
		// 输出格式
		$format = strtolower($request->get('format'));
		$format = $format ? : (IS_AJAX ? ($callback ? 'jsonp' : 'json') : NULL);
		// 设置响应
		View::setFormat($format);
		View::setCallback($callback);
	}
}