<?php

/**
 * 自定义响应对象
 * @author enychen
 */
namespace traits;

use \Yaf\Application;
use \Yaf\View_Interface;

class Response implements View_Interface {

	/**
	 * 视图引擎
	 * @var \Traits\View
	 */
	protected $engine = NULL;

	/**
	 * 构造函数
	 */
	public function __construct() {
		$this->engine = new View(NULL);
	}

	/**
	 * 渲染视图
	 * @param string $tpl 视图名称
	 * @param array $tpl_vars 视图要绑定的信息、
	 * @return string
	 */
	public function render($tpl, $tpl_vars = NULL) {
		return $this->engine->render($tpl, $tpl_vars);
	}

	/**
	 * 加载视图
	 * @param string $tpl 视图名称
	 * @param array $tpl_vars 视图要绑定的信息、
	 * @return string
	 */
	public function display($tpl, $tpl_vars = NULL) {
		return $this->engine->display($tpl, $tpl_vars);
	}

	/**
	 * 参数绑定
	 * @param string $name 键
	 * @param string $value 值
	 * @return void
	 */
	public function assign($name, $value = NULL) {
		$this->engine->assign($name, $value);
	}

	/**
	 * 设置视图目录
	 * @param string $view_directory 视图目录名称
	 * @return void
	 */
	public function setScriptPath($view_directory) {
		$this->engine->setScriptPath($view_directory);
	}

	/**
	 * 获取视图目录
	 * @return string
	 */
	public function getScriptPath() {
		$this->engine->getScriptPath();
	}
}

