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
	protected $metas = array();

	/**
	 * 设置通用模板名称
	 * @param string $template 模板名称
	 * @return View $this 返回当前对象进行连贯操作
	 */
	public function setTemplate($template) {
		$this->template = $template;
		return $this;
	}

	/**
	 * 获取通用模板名称
	 * @return string
	 */
	public function getTemplate() {
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
	 * @param array $metas meta头信息，key=>value的形式
	 * @return View $this 返回当前对象进行连贯操作
	 */
	public function setMetas(array $metas) {
		$this->metas = array_merge($this->metas, $metas);
		return $this;
	}

	/**
	 * 获取meta信息
	 * @return array
	 */
	public function getMetas() {
		return $this->metas;	
	}

	/**
	 * 设置css文件
	 * @return View $this 返回当前对象进行连贯操作
	 */
	public function setStyles() {
		$this->styles = array_merge($this->styles, func_get_args());
		return $this;
	}

	/**
	 * 获取css文件列表
	 * @return array
	 */
	public function getStyles() {
		return $this->styles;
	}

	/**
	 * 设置头部的js脚本文件
	 * @return View $this 返回当前对象进行连贯操作
	 */
	public function setHeadScripts() {
		$this->scripts['head'] = array_merge($this->scripts['head'], func_get_args());
		return $this;
	}

	/**
	 * 获取头部的js脚本文件列表
	 * @return array
	 */
	public function getHeadScripts() {
		return $this->scripts['head'];
	}

	/**
	 * 设置尾部的js脚本文件
	 * @return View $this 返回当前对象进行连贯操作
	 */
	public function setFootScripts() {
		$this->scripts['foot'] = array_merge($this->scripts['foot'], func_get_args());
		return $this;
	}

	/**
	 * 获取尾部的js脚本文件列表
	 * @return array
	 */
	public function getFootScripts() {
		return $this->scripts['foot'];
	}

	/**
	 * 加载模块layout文件
	 * @param string $tpl 模板名称
	 * @param array $tpl_vars 视图数据
	 * @return View $this 返回当前对象进行连贯操作
	 */
	public function moduleComponent($tpl, array $tpl_vars = array()) {
		$this->setScriptPath(sprintf('%slayout', MODULE_VIEW_PATH));
		echo parent::render("{$tpl}.phtml", $tpl_vars);
		return $this;
	}

	/**
	 * 加载公共获取组件
	 * @param string $tpl 模板名称
	 * @param array $tpl_vars 视图数据
	 * @return View $this 返回当前对象进行连贯操作
	 */
	public function component($tpl, array $tpl_vars = array()) {
		$this->setScriptPath(sprintf('%scomponent', COMMON_VIEW_PATH));
		echo parent::render("{$tpl}.phtml", $tpl_vars);
		return $this;
	}

	/**
	 * 简化isset($data) ? $data : NULL的作用
	 * @param array $data 数组数据
	 * @param array $key 要获取的key
	 * @param mixed $default 如果不存在则输出
	 * @return void
	 */
	public function echoIsset($data, $key, $default = NULL) {
		echo isset($data[$key]) ? $data[$key] : $default;
	}

	/**
	 * 格式化时间戳
	 */
	public function formatDate($timestamp) {
		return date('Y-m-d H:i:s', strtotime($timestamp));
	}
}