<?php

/**
 * 模板对象
 * @author enychen
 */
namespace traits;

use \Yaf\View\Simple;

class Template extends Simple {

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
	 * 搜索引擎优化
	 * @var string
	 */
	protected $canonical = NULL;

	/**
	 * 设置跨页面值
	 * @var array
	 */
	protected $values = array();

	/**
	 * 设置通用模板名称
	 * @param string $template 模板名称
	 * @return Template $this 返回当前对象进行连贯操作
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
	 * @return Template $this 返回当前对象进行连贯操作
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
	 * @return Template $this 返回当前对象进行连贯操作
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
	 * @return Template $this 返回当前对象进行连贯操作
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
	 * @return Template $this 返回当前对象进行连贯操作
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
	 * @return Template $this 返回当前对象进行连贯操作
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
	 * 设置搜索引擎优化内容
	 * @param string $canonical 引擎内容
	 * @return \traits\Template
	 */
	public function setCanonical($canonical) {
		$this->canonical = $canonical;
		return $this;
	}

	/**
	 * 获取搜索引擎优化内容
	 * @return string
	 */
	public function getCanonical() {
		return $this->canonical;
	}

	/**
	 * 设置跨页面值
	 * @param string $key 键
	 * @param string $value 值
	 * @return \traits\Template
	 */
	public function setValue($key, $value) {
		$this->values[$key] = $value;
		return $this;
	}

	/**
	 * 获取跨页面值
	 * @param string $key 键
	 * @param mixed $default　找不到的返回默认值
	 * @return mixed
	 */
	public function getValue($key, $default = NULL) {
		return isset($this->values[$key]) ? $this->values[$key] : $default;
	}

	/**
	 * 加载模块layout文件
	 * @param string $tpl 模板名称
	 * @param array $tpl_vars 视图数据
	 * @return Template $this 返回当前对象进行连贯操作
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
	 * @return Template $this 返回当前对象进行连贯操作
	 */
	public function component($tpl, array $tpl_vars = array()) {
		$this->setScriptPath(sprintf('%scomponent', COMMON_VIEW_PATH));
		echo parent::render("{$tpl}.phtml", $tpl_vars);
		return $this;
	}

	/**
	 * 格式化时间戳
	 * @param string $timestamp YmdHis的格式化字符串
	 * @param string $format 默认格式化字符串
	 * ＠return string
	 */
	public function formatYmdHis($timestamp, $format = 'Y-m-d H:i:s') {
		return date($format, strtotime($timestamp));
	}

	/**
	 * 将换行符转成<br/>
	 * @param string $string 字符串内容
	 * @return string
	 */
	public function WrapToBr($string) {
		return str_replace(chr(10), '<br/>', $string);
	}
}