<?php

/**
 * 所有模块控制基类的基类
 * @author enychen
 */
namespace base;

use \Yaf\Registry;
use \traits\Request;
use \Yaf\Application;
use \traits\FormException;
use \traits\NotifyException;
use \Yaf\Controller_Abstract;
use \traits\NotFoundException;
use \traits\RedirectException;
use \traits\ForbiddenException;

abstract class BaseController extends Controller_Abstract {

	/**
	 * 全局初始化
	 * @return void
	 */
	public final function init() {
		$this->initResponse();
		$this->initController();
	}

	/**
	 * 响应输出初始化
	 * @return void
	 */
	protected function initResponse() {
		Registry::set('view', $this->getView());
	}

	/**
	 * 控制器初始化
	 * @return void
	 */
	protected function initController() {
	}

	/**
	 * 获取经过验证请求对象
	 * @return \traits\Request 请求封装对象
	 */
	public function getRequest() {
		return Request::getInstance();
	}

	/**
	 * 关闭模板
	 * @return void
	 */
	protected final function disView() {
		Application::app()->getDispatcher()->disableView();
		$this->getView()->disableView();
	}

	/**
	 * 参数绑定
	 * @param string $key 键
	 * @param mixed $value 值
	 * @return void
	 */
	protected final function assign($key, $value) {
		$this->getView()->assign($key, $value);
	}

	/**
	 * 模板替换
	 * @param string $template 自定义模板
	 * @return void
	 */
	protected final function template($template) {
		$this->disView();
		$this->display($template);
	}

	/**
	 * json输出
	 * @param boolean $status 结果状态
	 * @param string $message 提示信息
	 * @param array $data 数据信息
	 * @param int $code 提示码
	 * @return void
	 */
	public final function json($status, $message, $code, $data = NULL) {
		$json['status'] = $status;
		$json['message'] = $message;
		$json['data'] = $data;
		$json['code'] = $code;
		$json = json_encode($json);
		header("Content-type: application/json; charset=UTF-8");
		exit($json);
	}

	/**
	 * jsonp输出
	 * @param string $callback 回调函数
	 * @param boolean $status 结果状态
	 * @param string $message 提示信息
	 * @param int $code 提示码
	 * @param array $data 数据信息
	 * @return void
	 */
	protected final function jsonp($callback, $status, $message, $code, $data = NULL) {
		$json['status'] = $status;
		$json['message'] = $message;
		$json['data'] = $data;
		$json['code'] = $code;
		$json = json_encode($json);
		exit("<script type='text/javascript'>{$callback}({$json})</script>");
	}

	/**
	 * 输出调试信息
	 * @param mixed $content 调试内容
	 * @return void
	 */
	protected final function debug($content) {
		exit(sprintf("<pre>%s</pre>", print_r($content, TRUE)));
	}

	/**
	 * 抛出403的异常
	 * @param number $code 错误码
	 * @param string $message 错误信息
	 * @throws ForbiddenException
	 * @return void
	 */
	public function throwForbiddenException($code, $message) {
		throw new ForbiddenException($message, $code);
	}

	/**
	 * 抛出404异常
	 * @param number $code 错误码
	 * @param string $message 错误信息
	 * @throws NotFoundException
	 * @return void
	 */
	public function throwNotFoundException($code, $message) {
		throw new NotFoundException($message, $code);
	}
	
	/**
	 * 抛出错误通知的异常
	 * @param number $code 错误码
	 * @param string $message 错误信息
	 * @throws NotifyException
	 * @return void
	 */
	public function throwNotifyException($code, $message) {
		throw new NotifyException($message, $code);
	}
	
	/**
	 * 抛出进行跳转的异常
	 * @param number $code 错误码
	 * @param string $message 错误信息
	 * @throws RedirectException
	 * @return void
	 */
	public function throwRedirectException($code, $message) {
		throw new RedirectException($message, $code);
	}

	/**
	 * 获取完整url路径
	 * @param string $encode
	 * @return string
	 */
	protected function getFullUrl($encode = TRUE) {
		$url = "http://{$_SERVER['SERVER_NAME']}{$_SERVER['REQUEST_URI']}";
		return $encode ? urlencode($url) : $url;
	}
}