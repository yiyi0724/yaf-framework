<?php

/**
 * 自定义视图接口
 */
namespace Traits;

use \Yaf\Application;
use \Yaf\View\Simple;
use \Yaf\View_Interface;

class ViewInterface implements View_Interface {

	/**
	 * 视图引擎
	 * @var \Html\View
	 */
	public $engine = NULL;

	/**
	 * 构造函数
	 */
	public function __construct() {
		$this->engine = new View(NULL);
	}

	/**
	 * 渲染视图
	 * @param string $viewPath
	 * @param array $tplVars
	 */
	public function render($tpl, $tpl_vars = NULL) {
		$this->engine->setScriptPath(MODULE_VIEW);
		return $this->engine->render($tpl, $tpl_vars);
	}

	public function display($tpl, $tpl_vars = NULL) {
		$this->engine->setScriptPath(MODULE_VIEW);		
		return $this->engine->display($tpl, $tpl_vars);
	}

	public function assign($name, $value = NULL) {
		$this->engine->assign($name, $value);
	}

	public function setScriptPath($view_directory) {
		$this->engine->setScriptPath($view_directory);
	}

	public function getScriptPath() {
		$this->engine->getScriptPath();
	}
}

class View extends Simple {
	
	/**
	 * 加载公共模板
	 * @param string $tpl 模板名称
	 * @param array $tpl_vars 视图数据
	 */
	public function layout($tpl, array $tpl_vars = array()) {
		echo $this->render(MODULE_PATH."views/layout/{$tpl}", $tpl_vars);
	}
	
	/**
	 * 将时间戳格式化
	 * @param string|int $time 时间
	 * @param string $format 格式化选项
	 * @return string 格式化的后的信息
	 */
	public function formatDate($time, $format = 'Y-m-d H:i:s') {
		$strtotime = @strtotime($time) ? strtotime($time) : $time;
		return date($format, $time);
	}
	
	/**
	 * 转义html
	 * @param string $string 待转义的信息
	 * @return string
	 */
	public function htmlEncode($string) {
		return htmlspecialchars($string, ENT_QUOTES | ENT_COMPAT | ENT_HTML401);
	}
	
	/**
	 * 取消转义html
	 * @param string $string 已经转义过的内容
	 * @return string
	 */
	public function htmlDecode($string) {
		return htmlspecialchars_decode($string);
	}
	
	/**
	 * 从一堆中获取一个type
	 * @param string $type 
	 * @param string $range
	 * @return Ambigous <NULL, unknown>
	 */
	public function type($type, $range) {
		return isset($range[$type]) ? $range[$type] : NULL;
	}
}

