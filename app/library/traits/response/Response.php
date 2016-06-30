<?php

/**
 * 自定义视图对象
 * @author enychen
 */
namespace traits\response;

use \Yaf\Application;
use \Yaf\View\Simple;
use traits\Form;

class Response extends Simple {

	/**
	 * 输出格式
	 * @var string
	 */
	protected static $format = NULL;
	
	/**
	 * 针对jsonp格式的回调函数名
	 * @var string
	 */
	protected static $callback = NULL;

	/**
	 * 视图数据
	 * @var array
	 */
	protected $_tpl_vars = array();

	/**
	 * 设置输出格式
	 * @param string $format 输出格式
	 * @return void
	 */
	public static function setFormat($format) {
		self::$format = $format;
	}
	
	/**
	 * 获取输出格式
	 * @return string
	 */
	public static function getFormat() {
		return self::$format;
	}
	
	/**
	 * 设置jsonp格式的回调函数名
	 * @param string $callback 回调函数名
	 * @return void
	 */
	public static function setCallback($callback) {
		self::$callback = $callback;
	}
	
	/**
	 * 获取jsonp格式的回调函数名
	 * @return string
	 */
	public static function getCallback() {
		return self::$callback;
	}

	/**
	 * 加载公共layout模板
	 * @param string $tpl 模板名称
	 * @param array $tpl_vars 视图数据
	 */
	public function layout($tpl, array $tpl_vars = array()) {
		$this->setScriptPath(COMMON_VIEW_PATH . 'layout');
		return parent::render($tpl, $tpl_vars);
	}

	/**
	 * 加载模块layout文件
	 * @param string $tpl 模板名称
	 * @param array $tpl_vars 视图数据
	 * @return void
	 */
	public function moduleLayout($tpl, array $tpl_vars = array()) {
		$this->setScriptPath(MODULE_VIEW_PATH . 'layout');
		return parent::render($tpl, $tpl_vars);
	}

	/**
	 * 渲染视图
	 * @param string $tpl 视图名称
	 * @param array|null $tpl_vars 视图要绑定的信息
	 * @return string
	 */
	public function render($tpl, $tpl_vars = NULL) {
		return $this->response($tpl, $tpl_vars, 'render');
	}

	/**
	 * 加载视图
	 * @param string $tpl 视图名称
	 * @param array|null $tpl_vars 视图要绑定的信息
	 * @return string
	 */
	public function display($tpl, $tpl_vars = NULL) {
		return $this->response($tpl, $tpl_vars, 'display');
	}

	/**
	 * 最终输出格式化样式
	 * @param string $tpl 视图名称
	 * @param array $tpl_vars 视图要绑定的信息
	 */
	protected final function response($tpl, $tplVars, $callback) {
		// 合并参数
		$tplVars = array_merge($this->_tpl_vars, (is_null($tplVars) ? array() : $tplVars));
		// 获取接受对象
		$accept = Application::app()->getDispatcher()->getRequest()->getServer('HTTP_ACCEPT');
		switch(TRUE) {
			case in_array(self::getFormat(), array('json', 'jsonp')):
				$this->jsonp($tplVars);
				break;
			default:
				parent::$callback($tpl, $tplVars);
				break;
		}
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
	protected final function jsonp($output) {
		$json['message'] = $output;
		$json['action'] = isset($action['action']);
		$json = json_encode($json);
	
		$header = 'application/json';
		if($callback = self::getCallback()) {
			$header = 'text/javascript';
			$json = "{$callback}({$json})";
		}
	
		Application::app()->getDispatcher()->disableView();
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
}