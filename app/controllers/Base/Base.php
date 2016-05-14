<?php

namespace Base;

/**
 * 所有模块控制基类的基类
 */
use \Yaf\Session;
use \Yaf\Config\Ini;
use \Yaf\Application;
use \Yaf\Controller_Abstract;

abstract class BaseController extends Controller_Abstract {

	/**
	 * 参数绑定
	 * @param string $key 键
	 * @param mixed $value 值
	 * @return void
	 */
	protected final function assign($key, $value) {
		$this->getView()->assign($key, $value);
	}

	/**
	 * 模板替换
	 * @param string $template 自定义模板
	 * @return void
	 */
	protected final function template($template) {
		$this->disView() and $this->display($template);
	}

	/**
	 * 关闭模板
	 * @return void
	 */
	protected final function disView() {
		Application::app()->getDispatcher()->disableView();
	}

	/**
	 * 跳转提示
	 * @param array $data 输出到页面的数据
	 * @param string $template 使用的模板
	 * @return void
	 */
	protected final function notify(array $notify = array(), $template = 'notify') {
		$view = $this->getView();
		$this->assign('notify', $notify);
		$view->setScriptPath(MODULE_PATH . 'views');
		$view->display("layout/{$template}.phtml");
		exit();
	}

	/**
	 * json|jsonp数据输出
	 * 1001 - 正确弹框提示
	 * 1002 - 警告弹框提示
	 * 1003 - 错误弹框提示
	 * 1010 - url地址跳转
	 * 1011 - 正确弹框并跳转
	 * 1012 - 警告弹框并跳转
	 * 1013 - 错误弹框并跳转
	 * 1020 - 表单错误
	 * @param int|string|array $output 要输出的数据
	 * @param int $code 通用代码
	 * @return void
	 */
	protected final function jsonp($output, $action = 1001) {
		$json['message'] = $output;
		$json['action'] = $action;
		$json = json_encode($json);
		
		$header = 'application/json';
		$jsonp = $this->getRequest()->get('callback', NULL);		
		if(preg_match('/^[a-zA-Z_][a-zA-Z0-9_\.]*$/', $jsonp)) {
			$header = 'text/javascript';
			$json = "{$jsonp}({$json})";
		}
		
		$this->disView();
		header("Content-type: {$header}; charset=UTF-8");
		exit($json);
	}
	
	/**
	 * 输出<script></script>标签
	 * @param string $content 要输出的script执行代码
	 * @return void
	 */
	protected final function scriptTab($content) {
		exit("<script type='text/javascript'>{$content}</script>");
	}

	/**
	 * 页面跳转(封装ajax跳转和http|form跳转)
	 * location-使用http头信息跳转, get-使用<meta>跳转，post-使用<form>跳转
	 * @param string $url 要跳转的url地址
	 * @param string $method 跳转方式，get|post|redirect
	 * @param array|int $data 如果是post请输入数组，如果是location请输入301|302|303|307,get则进行忽略
	 * @return void
	 */
	public final function redirect($url, $method = 'location', $other = array()) {
		IS_AJAX ? $this->jsonp($url, 1010) : \Network\Location::$method($url, $other);
	}

	/**
	 * 参数整合，清空全局变量，进行数据校验
	 * @param \Yaf\Request_Abstract $request 请求对象
	 * @param array $putOrDelete put和delete方法支持存放数组
	 * @return void
	 */
	protected function inputFliter($putOrDelete = array()) {
		// PUT和DETELE方法支持
		if(IS_PUT || IS_DELETE) {
			parse_str(file_get_contents('php://input'), $putOrDelete);
		}
		
		// 输入数据源
		$request = $this->getRequest();
		$params = array_merge($request->getParams(), $putOrDelete, $_REQUEST);
		unset($_GET, $_POST, $_REQUEST);
		
		// 获取检查规则
		list($controller, $action) = array(CONTROLLER . 'Form', ACTION . 'Input');
		if(is_file(FORM_FILE) && require (FORM_FILE)) {
			if(!method_exists($controller, $action)) {
				return array();
			}
			$rules = $controller::$action();
			$formLib = new \Security\Form();
			$formLib->setRequestMethod($request->getMethod());
			$formLib->setRules($rules);
			$formLib->setParams($params);
			if($error = $formLib->fliter()) {
				IS_AJAX ? $this->jsonp($error, 1020) : $this->notify($error);
				exit();
			}
			$params = $formLib->getSuccess();
		}
		
		return $params;
	}

	/**
	 * 获取session对象
	 * @return \Yaf\Session
	 */
	public final function getSession() {
		return Session::getInstance();
	}

	/**
	 * 读取配置信息
	 * @param array $key 键名
	 * @return string|object|NULL 返回配置信息
	 */
	public final function getConfig($key) {
		return Application::app()->getConfig()->get($key);
	}

	/**
	 * 加载ini配置文件
	 * @param string $ini 文件名，不需要包含.ini后缀
	 * @return \Yaf\Config\Ini ini对象
	 */
	public final function loadIni($ini) {
		return new Ini(CONF_PATH . "{$ini}.ini", \YAF\ENVIRON);
	}
}