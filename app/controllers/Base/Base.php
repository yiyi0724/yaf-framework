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
	 * json|jsonp数据输出
	 * @param array $output 要输出的数据
	 * @param int $code 通用代码 200-弹出提示框，301-url地址跳转，302|弹框并进行页面跳转，412-提示表单错误
	 * @return void
	 */
	protected final function jsonp($output, $action = 200) {
		// 关闭视图
		$this->disView();
		
		// 数据整理
		$json['message'] = $output;
		$json['action'] = $action;
		$json = json_encode($json);
		
		// jsonp回调函数, 检查函数名
		$jsonp = $this->getRequest()->get('callback', NULL);
		if(preg_match('/^[a-zA-Z_][a-zA-Z0-9_\.]*$/', $jsonp)) {
			$header = 'text/javascript';
			$json = "{$jsonp}({$json})";
		} else {
			$header = 'application/json';
		}
		
		// 结果输出
		header("Content-type: {$header}; charset=UTF-8");
		echo $json;
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
	}

	/**
	 * 页面跳转
	 * redirect-使用http头信息跳转, get-使用<meta>跳转，post-使用<form>跳转
	 * @param string $url 要跳转的url地址
	 * @param string $method 跳转方式，get|post|redirect
	 * @param array|int $data 如果是post请输入数组，如果是redirect请输入301|302|303|307,get则进行忽略
	 * @return void
	 */
	protected final function location($url, $method = 'get', $other = array()) {
		\Network\Location::$method($url, $other);
	}

	/**
	 * 关闭模板
	 * @return void
	 */
	protected final function disView() {
		Application::app()->getDispatcher()->disableView();
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
		if(is_file(FORM_FILE) && require (FORM_FILE)) {
			if(method_exists(CONTROLLER . 'Form', ACTION . 'Input')) {
				$rules = call_user_func(CONTROLLER . 'Form::' . ACTION . 'Input');
				$formLib = new \Security\Form();
				$formLib->setRequestMethod($request->getMethod());
				$formLib->setRules($rules);
				$formLib->setParams($params);
				if($error = $formLib->fliter()) {
					IS_AJAX ? $this->jsonp($error, 412) : $this->notify($error);
					exit();
				}
				$params = $formLib->getSuccess();
			}
		}
		
		return $params;
	}
}