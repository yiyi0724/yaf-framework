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
	 */
	protected function validity() {
		// 初始化参数
		require (MODULE_PATH . 'validates/' . CONTROLLER . 'Form.php');
		$controller = CONTROLLER . 'Form';
		$action = ACTION;
		$checks = $controller::$action();
		$inputs = $this->getRequest()->getParams();
		
		// 数据校验
		$inputs = Form::check($checks, $inputs);
		
		// 存在错误进行提示
		if($inputs[1]) {
			if(IS_AJAX) {
				$this->jsonp($inputs[1], 412);
			}
			else {
				$this->template(array(
					'form'=>$inputs[1]
				), 'common/notify', TRUE);
			}
		}
		
		return $inputs[0];
	}

	/**
	 * 加载模板
	 * @param array $output 参数绑定
	 * @param string $template 自定义模板
	 * @param bool $useView 是否使用通用模板
	 */
	protected function template(array $output, $template = NULL, $useView = FALSE) {
		// 数据绑定
		$view = $this->getView();
		foreach($output as $key=>$value) {
			$view->assign($key, $value);
		}
		
		// 模板替换
		if($template) {
			$this->disView();
			$view ? $view->display("{$template}.phtml") : $this->display($template);
		}
		exit();
	}

	/**
	 * 数据输出
	 * @param array $output 要输出的数据
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
	 * 页面跳转
	 * @param string $url 要跳转的url地址
	 * @param string $method 跳转方式，get | post |redirect
	 * @param array|int $data 如果是post请输入数组，如果是redirect请输入301|302|303|307	 
	 */
	protected function location($url, $method = 'get', $data = array()) {
		IS_AJAX ? $this->jsonp($url, 301) : Location::$method($url, $data);
	}
	
	protected function disView() {
		Application::app()->getDispatcher()->disableView();
	}
}