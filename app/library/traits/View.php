<?php

/**
 * 模板对象
 * @author enychen
 */
namespace traits;

use \Yaf\View\Simple;

class view extends Simple {

	/**
	 * 加载公共layout模板
	 * @param string $tpl 模板名称
	 * @param array $tpl_vars 视图数据
	 * @return void
	 */
	public function commonLayout($tpl, array $tpl_vars = array()) {
		$this->setScriptPath(sprintf('%slayout', COMMON_VIEW_PATH));
		echo parent::render("{$tpl}.phtml", $tpl_vars);
	}

	/**
	 * 加载模块layout文件
	 * @param string $tpl 模板名称
	 * @param array $tpl_vars 视图数据
	 * @return void
	 */
	public function moduleLayout($tpl, array $tpl_vars = array()) {
		$this->setScriptPath(sprintf('%slayout', MODULE_VIEW_PATH));
		echo parent::render("{$tpl}.phtml", $tpl_vars);
	}

	public function component($tpl, array $tpl_vars = array()) {
		$this->setScriptPath(sprintf('%scomponent', COMMON_VIEW_PATH));
		echo parent::render("{$tpl}.phtml", $tpl_vars);
	}

	/**
	 * 简化isset($data) ? $data : NULL的作用
	 * @param array $data 数组数据
	 * @param array $key 要获取的key
	 * @param mixed $default 如果不存在则输出
	 * @return mixed
	 */
	public function simplifyIsset($data, $key, $default = NULL) {
		return isset($data[$key]) ? $data[$key] : $default;
	}
}