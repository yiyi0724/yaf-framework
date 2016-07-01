<?php

/**
 * 输出对象
 */
namespace traits;

use \Yaf\Application;
use \Yaf\View\Simple;

class view extends Simple {

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
	public function commonLayout($tpl, array $tpl_vars = array()) {
		$this->setScriptPath(sprintf('%slayout', COMMON_VIEW_PATH));
		echo parent::render($tpl, $tpl_vars);
	}

	/**
	 * 加载模块layout文件
	 * @param string $tpl 模板名称
	 * @param array $tpl_vars 视图数据
	 * @return void
	 */
	public function moduleLayout($tpl, array $tpl_vars = array()) {
		$this->setScriptPath(sprintf('%slayout', MODULE_VIEW_PATH));
		echo parent::render($tpl, $tpl_vars);
	}

	/**
	 * 渲染视图
	 * @param string $tpl 视图名称
	 * @param array|null $tpl_vars 视图要绑定的信息
	 * @return string
	 */
	public function render($tpl, $tpl_vars = NULL) {
		$tplVars = array_merge($this->_tpl_vars, (is_null($tpl_vars) ? array() : $tpl_vars));
		return parent::render($tpl, $tplVars);
	}

	/**
	 * 加载视图
	 * @param string $tpl 视图名称
	 * @param array|null $tpl_vars 视图要绑定的信息
	 * @return string
	 */
	public function display($tpl, $tpl_vars = NULL) {
		$tplVars = array_merge($this->_tpl_vars, (is_null($tpl_vars) ? array() : $tpl_vars));
		return parent::display($tpl, $tplVars);
	}
}