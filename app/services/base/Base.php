<?php

/**
 * 逻辑组件基类
 * @author enychen
 */
namespace services\base;

use \Yaf\Session;
use \traits\Request;
use \Yaf\Config\Ini;
use \Yaf\Application;
use \traits\FormException;
use \traits\NotifyException;
use \traits\NotFoundException;
use \traits\RedirectException;
use \traits\ForbiddenException;

class Base {

	/**
	 * 获取session对象
	 * @return \Yaf\Session
	 */
	public final function getSession() {
		return Session::getInstance();
	}

	/**
	 * 读取配置信息
	 * @param array $key 键名
	 * @return string|object|NULL 返回配置信息
	 */
	public final function getConfig($key) {
		return Application::app()->getConfig()->get($key);
	}

	/**
	 * 加载ini配置文件
	 * @param string $ini 文件名，不需要包含.ini后缀
	 * @return \Yaf\Config\Ini ini对象
	 */
	public final function loadIni($ini) {
		return new Ini(CONF_PATH . "{$ini}.ini", \YAF\ENVIRON);
	}

	/**
	 * 获取默认未进行验证的内置请求对象
	 * @return \Yaf\Request_Abstract
	 */
	public function getYafRequest() {
		return Application::app()->getDispatcher()->getRequest();
	}
	
	/**
	 * 获取经过验证请求对象
	 * @return \traits\Request 请求封装对象
	 */
	public function getRequest() {
		return Request::getInstance();
	}

	/**
	 * 抛出禁止访问的异常
	 * @param string $message 错误信息
	 * @param number $code 错误码
	 * @throws ForbiddenException
	 * @return void
	 */
	public function throwForbiddenException($message, $code = 0) {
		throw new ForbiddenException($message, $code);
	}

	/**
	 * 抛出404异常
	 * @param string $message 错误信息
	 * @param number $code 错误码
	 * @throws NotFoundException
	 * @return void
	 */
	public function throwNotFoundException($message, $code = 0) {
		throw new NotFoundException($message, $code);
	}

	/**
	 * 抛出错误通知的异常
	 * @param string $message 错误信息
	 * @param number $code 错误码
	 * @throws NotifyException
	 * @return void
	 */
	public function throwNotifyException($message, $code = 0) {
		throw new NotifyException($message, $code);
	}

	/**
	 * 抛出进行跳转的异常
	 * @param string $message 错误信息
	 * @param number $code 错误码
	 * @throws RedirectException
	 * @return void
	 */
	public function throwRedirectException($message, $code = 0) {
		throw new RedirectException($message, $code);
	}
}