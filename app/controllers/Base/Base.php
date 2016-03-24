<?php

namespace Base;

/**
 * 所有模块控制基类的基类
 */
use \Yaf\Controller_Abstract;
use \Yaf\Application;
use \Yaf\Session;
use \Security\Form;
use \Network\Location;

abstract class BaseController extends Controller_Abstract {

	/**
	 * 数据合法性检查
	 * @return array 通过检查的数据
	 */
	protected function validity() {
		// 初始化参数
		require (MODULE_PATH . 'validates/' . CONTROLLER . 'Form.php');
		$controller = CONTROLLER . 'Form';
		$action = ACTION;
		$checks = $controller::$action();
		$inputs = $this->getRequest()->getParams();
		
		// 数据校验
		list($success, $fail) = Form::check($checks, $inputs);
		
		// 存在错误进行提示
		if($fail) {
			if(IS_AJAX) {
				$this->jsonp($inputs[1], 412);
			} else {
				$this->assign('form', $inputs[1]);
				$this->template('common/notify', TRUE);
				exit();
			}
		}
		
		return $success;
	}

	/**
	 * 模板替换
	 * @param string $template 自定义模板
	 * @param bool $common 是否使用通用模板
	 * @return void;
	 */
	protected function template($template, $common = FALSE) {
		$this->disView();
		$common ? $this->getView()->display("{$template}.phtml") : $this->display($template);
	}

	/**
	 * json|jsonp数据输出
	 * @param array $output 要输出的数据
	 * @param int $code 通用代码
	 * @return void
	 */
	public function jsonp($output, $code = 200) {
		// 数据整理
		$json['message'] = $output;
		$json['code'] = $code;
		$json = json_encode($json);
		
		// jsonp回调函数, 检查函数名
		$jsonp = $this->getRequest()->get('callback', NULL);
		if(preg_match('/^[a-zA-Z_][a-zA-Z0-9_\.]*$/', $jsonp)) {
			$header = 'text/javascript';
			$json = "{$jsonp}({$json})";
		}
		else {
			$header = 'application/json';
		}
		
		// 结果输出
		header("Content-type: {$header}; charset=UTF-8");
		exit($json);
	}

	/**
	 * 参数绑定
	 * @param string $key 键
	 * @param string $value 值
	 * @return void
	 */
	protected function assign($key, $value) {
		$this->getView()->assign($key, $value);
	}

	/**
	 * 页面跳转
	 * @param string $url 要跳转的url地址
	 * @param string $method 跳转方式，get|post|redirect
	 * @param array|int $data 如果是post请输入数组，如果是redirect请输入301|302|303|307
	 * @return void
	 */
	protected function location($url, $method = 'get', $data = array()) {
		IS_AJAX ? $this->jsonp($url, 302) : Location::$method($url, $data);
	}

	/**
	 * 关闭模板
	 * @return void
	 */
	protected function disView() {
		Application::app()->getDispatcher()->disableView();
	}
}