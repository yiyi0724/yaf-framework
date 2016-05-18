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
		$this->engine = new View(APPLICATION_PATH);
	}

	/**
	 * 渲染视图
	 * @param string $viewPath
	 * @param array $tplVars
	 */
	public function render($tpl, $tpl_vars = NULL) {
		$this->engine->setScriptPath(MODULE_PATH . 'views');
		return $this->engine->render($tpl, $tpl_vars);
	}

	public function display($tpl, $tpl_vars = NULL) {
		$this->engine->setScriptPath(MODULE_PATH . 'views');
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
}

