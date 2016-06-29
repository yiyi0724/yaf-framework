<?php

namespace base;

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
	protected final function notify($notify = NULL, $template = 'notify') {
		$view = $this->getView();
		$this->assign('notify', $notify);
		$view->setScriptPath(MODULE_PATH . 'views');
		$view->display("error/{$template}.phtml");
		exit();
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
		IS_AJAX ? $this->jsonp($url, 1010) : \network\Location::$method($url, $other);
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
		
		// 获取检查规则
		list($controller, $action) = array(
			CONTROLLER . 'Form', ACTION . 'Input'
		);
		if(is_file(FORM_FILE) && require (FORM_FILE)) {
			if(!method_exists($controller, $action)) {
				return array();
			}
			$rules = $controller::$action();
			$formLib = new \security\Form();
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
}