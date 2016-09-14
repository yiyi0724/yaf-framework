<?php

/**
 * 自定义响应对象
 * @author enychen
 */
namespace traits;

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
		$this->setEngine(new Template(NULL));
	}
	
	/**
	 * 设置视图响应对象
	 * @param \traits\View $engine 视图引擎对象
	 * @return void
	 */
	public function setEngine(\traits\Template $engine) {
		$this->engine = $engine;
	}

	/**
	 * 获取视图响应对象
	 * @return \traits\View $engine 视图引擎对象
	 */
	public function getEngine() {
		return $this->engine;
	}

	/**
	 * 渲染视图
	 * @param string $tpl 视图名称
	 * @param array $tpl_vars 视图要绑定的信息、
	 * @return string
	 */
	public function render($tpl, $tpl_vars = NULL) {
		$this->assign('body', $this->getEngine()->render($tpl, $tpl_vars));
		$this->getEngine()->setScriptPath(sprintf('%slayout', COMMON_VIEW_PATH));
		return $this->getEngine()->render("{$this->getEngine()->getTemplate()}.phtml");
	}

	/**
	 * 加载视图
	 * @param string $tpl 视图名称
	 * @param array $tpl_vars 视图要绑定的信息、
	 * @return string
	 */
	public function display($tpl, $tpl_vars = NULL) {
		$this->assign('body', $this->getEngine()->display($tpl, $tpl_vars));
		$this->getEngine()->setScriptPath(sprintf('%slayout', COMMON_VIEW_PATH));
		return $this->getEngine()->render("{$this->getEngine()->getTemplate()}.phtml");
	}

	/**
	 * 参数绑定
	 * @param string $name 键
	 * @param string $value 值
	 * @return void
	 */
	public function assign($name, $value = NULL) {
		$this->getEngine()->assign($name, $value);
	}

	/**
	 * 设置视图目录
	 * @param string $view_directory 视图目录名称
	 * @return void
	 */
	public function setScriptPath($view_directory) {
		$this->getEngine()->setScriptPath($view_directory);
	}

	/**
	 * 获取视图目录
	 * @return string
	 */
	public function getScriptPath() {
		$this->getEngine()->getScriptPath();
	}
}