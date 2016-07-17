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
	 * 获取经过验证请求对象
	 * @return \traits\Request 请求封装对象
	 */
	public function getVailRequest() {
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

		$callback = $this->getRequest()->get('callback');
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