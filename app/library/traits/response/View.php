<?php

/**
 * 自定义视图对象
 * @author enychen
 */
namespace traits\response;

use \Yaf\Application;
use \Yaf\View\Simple;

class View extends Simple {

	/**
	 * 视图数据
	 * @var array
	 */
	protected $_tpl_vars = array();

	/**
	 * 加载公共layout模板
	 * @param string $tpl 模板名称
	 * @param array $tpl_vars 视图数据
	 */
	public function layout($tpl, array $tpl_vars = array()) {
		$this->setScriptPath(rtrim(COMMON_VIEW_PATH, DS));
		return parent::render($tpl, $tpl_vars);
	}

	/**
	 * 加载模块layout文件
	 * @param string $tpl 模板名称
	 * @param array $tpl_vars 视图数据
	 * @return void
	 */
	public function moduleLayout($tpl, array $tpl_vars = array()) {
		$this->setScriptPath(rtrim(MODULE_VIEW_PATH, DS));
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
		if(is_array($tplVars)) {
			$tplVars = array_merge($this->_tpl_vars, $tplVars);
		}
		// 获取接受对象
		$accept = Application::app()->getDispatcher()->getRequest()->getServer('HTTP_ACCEPT');
		switch(TRUE) {
			case stripos($accept, 'text/javascript') !== FALSE:
			case stripos($accept, 'application/javascript') !== FALSE:
				// jsonp返回
				$tplVars['callback'] = IS_SCRIPT;
			case stripos($accept, 'text/json') !== FALSE:
			case stripos($accept, 'application/json') !== FALSE:
				// json返回
				$this->jsonp($tplVars);
				break;
			default:
				// 页面输出
				$this->setScriptPath(rtrim(MODULE_VIEW_PATH, DS));
				return parent::$callback($tpl, $tplVars);
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
	protected final function jsonp(array $output) {
		$json['message'] = $output;
		$json['action'] = isset($action['action']);
		$json = json_encode($json);
	
		$header = 'application/json';
		if(preg_match('/^[a-zA-Z_][a-zA-Z0-9_\.]*$/', IS_SCRIPT)) {
			$header = 'text/javascript';
			$json = "{$jsonp}({$json})";
		}

		Application::app()->getDispatcher()->disableView();
		header("Content-type: {$header}; charset=UTF-8");
		exit($json);
	}
}