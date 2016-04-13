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
	 * @param int $code 通用代码
	 * @return void
	 */
	protected final function jsonp($output, $code = 200) {
		// 关闭视图
		$this->disView();
		
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
		echo $json;
	}

	/**
	 * 跳转提示
	 * @param array $data 输出到页面的数据
	 * @param string $template 使用的模板
	 * @return void
	 */
	protected final function notify($notify = array(), $template = 'notify') {
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
	protected final function location($url, $method = 'get', $data = array()) {
		\Network\Location::$method($url, $data);
	}

	/**
	 * 关闭模板
	 * @return void
	 */
	protected final function disView() {
		Application::app()->getDispatcher()->disableView();
	}

	/**
	 * 获取session对象
	 * @return \Yaf\Session
	 */
	protected final function getSession() {
		return Session::getInstance();
	}

	/**
	 * 读取配置信息
	 * @param array $key 键名
	 * @return string|object
	 */
	protected final function getConfig($key) {
		return Application::app()->getConfig()->get($key);
	}

	/**
	 * 加载ini配置文件
	 * @param string $ini 文件名，不需要.ini后缀
	 * @return \Yaf\Config\Ini
	 */
	protected final function loadIni($ini) {
		return new Ini(CONF_PATH . "{$ini}.ini", Application::app()->environ());
	}
}