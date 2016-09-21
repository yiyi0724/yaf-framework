<?php

/**
 * 自定义响应对象
 * @author enychen
 * @version 1.0
 */
namespace traits;

use \Yaf\Application;
use \Yaf\View_Interface;

class Response implements View_Interface {

	/**
	 * 模板引擎
	 * @var \traits\Template
	 */
	protected $template = NULL;

	/**
	 * 构造函数，生成模板引擎
	 */
	public function __construct() {
		$this->setTemplate(new Template(NULL));
	}

	/**
	 * 设置视图响应对象
	 * @param \traits\Template $template 视图引擎对象
	 * @return Response $this 返回当前对象进行连贯操作
	 */
	public function setTemplate(Template $template) {
		$this->template = $template;
		return $this;
	}

	/**
	 * 获取视图响应对象
	 * @return \traits\Template 视图引擎对象
	 */
	public function getTemplate() {
		return $this->template;
	}

	/**
	 * 渲染视图
	 * @param string $tpl 视图名称
	 * @param array|null $tpl_vars 视图要绑定的信息
	 * @return string
	 */
	public function render($tpl, $tpl_vars = NULL) {
		// 公共参数信息
		$template = $this->getTemplate();
		$body = $template->render($tpl, $tpl_vars);
		// 是否需要装饰布局
		return $template->getLayout() ? $this->decorate($body) : $body;
	}

	/**
	 * 切换视图
	 * @param string $tpl 视图名称
	 * @param array $tpl_vars 视图要绑定的信息
	 * @return void
	 */
	public function display($tpl, $tpl_vars = NULL) {
		// 关闭自动渲染模板
		Application::app()->getDispatcher()->disableView();
		// 公共参数信息
		$template = $this->getTemplate();
		$body = $template->render($tpl, $tpl_vars);
		// 是否需要装饰布局
		echo $template->getLayout() ? $this->decorate($body) : $body;
	}

	/**
	 * 装饰模板布局
	 * @param string $body 当前页面数据
	 * @return string
	 */
	protected function decorate($body) {
		$template = $this->getTemplate();
		$template->setScriptPath(sprintf('%slayout', COMMON_VIEW_PATH));
		return $template->render("{$template->getLayout()}.phtml");
	}

	/**
	 * 参数绑定
	 * @param string $name 键
	 * @param string|null $value 值
	 * @return void
	 */
	public function assign($name, $value = NULL) {
		$this->getTemplate()->assign($name, $value);
	}

	/**
	 * 设置视图目录
	 * @param string $view_directory 视图目录名称
	 * @return void
	 */
	public function setScriptPath($view_directory) {
		$this->getTemplate()->setScriptPath($view_directory);
	}

	/**
	 * 获取视图目录
	 * @return string
	 */
	public function getScriptPath() {
		$this->getTemplate()->getScriptPath();
	}
}