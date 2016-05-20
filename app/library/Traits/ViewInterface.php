<?php

/**
 * 自定义视图接口
 * @author enychen
 */
namespace Traits;

use \Yaf\Application;
use \Yaf\View\Simple;
use \Yaf\View_Interface;

class ViewInterface implements View_Interface {

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
		$this->setScriptPath(VIEW_PATH);
		return $this->engine->render($tpl, $tpl_vars);
	}

	/**
	 * 加载视图
	 * @param string $tpl 视图名称
	 * @param array $tpl_vars 视图要绑定的信息、
	 * @return string
	 */
	public function display($tpl, $tpl_vars = NULL) {
		$this->setScriptPath(VIEW_PATH);
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
		echo $this->render(APPLICATION_PATH . "views/layout/{$tpl}", $tpl_vars);
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
	 * 将时间戳格式化
	 * @param string|int $time 时间
	 * @param string $format 格式化选项
	 * @return string 格式化的后的信息
	 */
	public function formatDate($time, $format = 'Y-m-d H:i:s') {
		return date($format, (strtotime($time) ?  : $time));
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
	 * @param int|string $type 类型键
	 * @param array $range 类型键的区间
	 * @return mixed
	 */
	public function type($type, $range) {
		return isset($range[$type]) ? $range[$type] : NULL;
	}

	/**
	 * 将ip整数戳格式化
	 * @param int $ip 整数ip
	 * @return string
	 */
	public function long2ip($ip) {
		return long2ip($ip);
	}
}