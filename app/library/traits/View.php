<?php

/**
 * 模板对象
 * @author enychen
 */
namespace traits;

use \Yaf\View\Simple;

class View extends Simple {

	/**
	 * 通用模板名称
	 * @var string
	 */
	protected $template = 'main';

	/**
	 * 网站默认标题
	 * @var string
	 */
	protected $title = 'eny.Inc';

	/**
	 * 附件的css文件
	 * @var array
	 */
	protected $styles = array();

	/**
	 * 附件的js文件
	 * @var array
	 */
	protected $scripts = array(
		'head' => array(),
		'foot' => array(),
	);

	/**
	 * 附件的meta信息
	 * @var array
	 */
	protected $meta = array();

	/**
	 * 设置通用模板名称
	 * @param string $template 模板名称
	 * @return View $this 返回当前对象进行连贯操作
	 */
	public function setMain($template) {
		$this->template = $template;
		return $this;
	}

	/**
	 * 获取通用模板名称
	 * @return string
	 */
	public function getMain() {
		return $this->template;
	}

	/**
	 * 设置标题名称
	 * @param string $title 标题名称
	 * @return View $this 返回当前对象进行连贯操作
	 */
	public function setTitle($title) {
		$this->title = $title;
		return $this;
	}

	/**
	 * 获取标题名称
	 * @return string
	 */
	public function getTitle() {
		return $this->title;
	}

	/**
	 * 设置meta信息
	 */
	public function setMetas() {
		
	}

	public function getMetas() {
		
	}

	public function setStyles() {
		$this->styles = array_merge($this->styles, func_get_args());
		return $this;
	}

	public function getStyles() {
		return $this->styles;
	}

	public function setHeadScripts() {
		$this->scripts['head'] = array_merge($this->scripts['head'], func_get_args());
		return $this;
	}

	public function getHeadScripts() {
		return $this->scripts['head'];
	}
	
	public function setFootScripts() {
		$this->scripts['foot'] = array_merge($this->scripts['foot'], func_get_args());
		return $this;
	}

	public function getFootScripts() {
		return $this->scripts['foot'];
	}

	/**
	 * 加载模块layout文件
	 * @param string $tpl 模板名称
	 * @param array $tpl_vars 视图数据
	 * @return void
	 */
	public function moduleComponent($tpl, array $tpl_vars = array()) {
		$this->setScriptPath(sprintf('%slayout', MODULE_VIEW_PATH));
		echo parent::render("{$tpl}.phtml", $tpl_vars);
	}

	/**
	 * 获取组件
	 * @param unknown $tpl
	 * @param array $tpl_vars
	 */
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