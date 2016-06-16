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
		echo $this->render(APP_PATH . "views/layout/{$tpl}", $tpl_vars);
	}

	/**
	 * 加载模块layout文件
	 * @param string $tpl 模板名称
	 * @param array $tpl_vars 视图数据
	 * @return void
	 */
	public function moduleLayout($tpl, array $tpl_vars = array()) {
		echo $this->render(MODULE_PATH . "views/layout/{$tpl}", $tpl_vars);
	}

	/**
	 * 渲染视图
	 * @param string $tpl 视图名称
	 * @param array $tpl_vars 视图要绑定的信息
	 * @return string
	 */
	public function render($tpl, $tpl_vars = NULL) {
		if(!IS_AJAX) {
			
		} else {
			$this->setScriptPath(VIEW_PATH);
			return parent::render($tpl, $tpl_vars);
		}
	}

	/**
	 * 加载视图
	 * @param string $tpl 视图名称
	 * @param array $tpl_vars 视图要绑定的信息、
	 * @return string
	 */
	public function display($tpl, $tpl_vars = NULL) {
		if(IS_AJAX) {
				
		} else {
			$this->setScriptPath(VIEW_PATH);
			return parent::display($tpl, $tpl_vars);
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
	protected final function jsonp($output, $action = 1001) {
		$json['message'] = $output;
		$json['action'] = $action;
		$json = json_encode($json);
	
		$header = 'application/json';
		if(preg_match('/^[a-zA-Z_][a-zA-Z0-9_\.]*$/', IS_SCRIPT)) {
			$header = 'text/javascript';
			$json = "{$jsonp}({$json})";
		}

		$this->disView();
		header("Content-type: {$header}; charset=UTF-8");
		exit($json);
	}
}