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
	 * 获取请求对象
	 * @return \traits\Request 请求封装对象
	 */
	public function getRequest() {
		return \traits\Request::getInstance();
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
		$this->display($template) and $this->disView();
	}

	/**
	 * 关闭模板
	 * @return void
	 */
	protected final function disView() {
		Application::app()->getDispatcher()->disableView();
	}

	/**
	 * 页面跳转(封装ajax跳转和http|form跳转)
	 * location-使用http头信息跳转, get-使用<meta>跳转，post-使用<form>跳转
	 * @param string $url 要跳转的url地址
	 * @param string $method 跳转方式，get|post|redirect
	 * @param array|int $data 如果是post请输入数组，如果是location请输入301|302|303|307,get则进行忽略
	 * @return void
	 */
	public final function redirect($url, $method = 'get', $other = array()) {
		IS_AJAX ? $this->jsonp($url, 1010) : \network\Location::$method($url, $other);
	}
}