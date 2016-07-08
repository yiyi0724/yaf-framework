<?php

/**
 * 所有模块控制基类的基类
 * @author enychen
 */
namespace base;

use \Yaf\Session;
use \Yaf\Application;
use \Yaf\Controller_Abstract;

abstract class BaseController extends Controller_Abstract {

	/**
	 * 控制器初始化
	 * @return void
	 */
	public function init() {
		// 定义UID常量
		$memberServices = new \services\member\Base();
		$memberServices->initUid();
	}

	/**
	 * 获取经过验证请求对象
	 * @return \traits\Request 请求封装对象
	 */
	public function getRequest() {
		return \traits\Request::getInstance();
	}

	/**
	 * 关闭模板
	 * @return void
	 */
	protected final function disView() {
		Application::app()->getDispatcher()->disableView();
	}

	/**
	 * 页面跳转
	 * @param string $url 要跳转的url地址
	 * @param string $method 跳转方式，location-使用http头信息跳转, get-使用<meta>跳转，post-使用<form>跳转
	 * @param array|int $data 如果是post请输入数组，如果是location请输入301|302|303|307,get则进行忽略
	 * @return void
	 */
	public final function redirect($url, $method = 'get', $other = array()) {
		\network\Location::$method($url, $other);
	}

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
		$this->disView();
		$this->display($template);
	}

	/**
	 * json输出
	 * @param boolean $status 结果状态
	 * @param string $message 提示信息
	 * @param array $data 数据信息
	 * @return void
	 */
	public final function json($status, $message, $data = NULL) {
		$json['status'] = $status;
		$json['message'] = $message;
		$json['data'] = $data;
		$json = json_encode($json);

		$callback = Application::app()->getDispatcher()->getRequest()->get('callback');
		if(preg_match('/^[a-zA-Z_][a-zA-Z0-9_\.]*$/', $callback)) {
			// jsonp
			exit("<script type='text/javascript'>{$callback}({$json})</script>");
		} else {
			// json
			header("Content-type: application/json; charset=UTF-8");
			exit($json);
		}
	}

	/**
	 * 输出调试信息
	 * @param mixed $content 调试内容
	 * @return void
	 */
	protected function debug($content) {
		echo '<pre>';
		print_r($content);
		echo '</pre>';
		exit;
	}
}