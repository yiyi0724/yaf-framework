<?php

/**
 * 所有模块控制基类的基类
 * @author enychen
 */
namespace base;

use \Traits\Request;
use \Yaf\Application;
use \Traits\FormException;
use \Traits\NotifyException;
use \Yaf\Controller_Abstract;
use \Traits\NotFoundException;
use \Traits\RedirectException;
use \Traits\ForbiddenException;

abstract class BaseController extends Controller_Abstract {

	/**
	 * 获取经过验证请求对象
	 * @return \traits\Request 请求封装对象
	 */
	public final function getRequest() {
		return Request::getInstance();
	}

	/**
	 * 视图参数绑定
	 * @param string $key 键
	 * @param mixed $value 值
	 * @return void
	 */
	protected final function assign($key, $value) {
		$this->getView()->assign($key, $value);
	}

	/**
	 * 关闭模板
	 * @return void
	 */
	protected final function disView() {
		Application::app()->getDispatcher()->disableView();
	}

	/**
	 * json或jsonp输出
	 * @param boolean $status 结果状态
	 * @param string $message 提示信息
	 * @param array $data 数据信息
	 * @param int $code 提示码
	 * @return void
	 */
	protected final function json($status, $message, $code, $data = NULL) {
		$json['status'] = $status;
		$json['message'] = $message;
		$json['data'] = $data;
		$json['code'] = $code;
		$json = json_encode($json);
		if($callback = $this->getRequest()->get('callback')) {
			exit("<script type='text/javascript'>{$callback}({$json})</script>");
		} else {
			header("Content-type: application/json; charset=UTF-8");
			exit($json);
		}
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
	protected final function throwForbiddenException($code, $message) {
		throw new ForbiddenException($message, $code);
	}

	/**
	 * 抛出404异常
	 * @param number $code 错误码
	 * @param string $message 错误信息
	 * @throws NotFoundException
	 * @return void
	 */
	protected final function throwNotFoundException($code, $message) {
		throw new NotFoundException($message, $code);
	}

	/**
	 * 抛出错误通知的异常
	 * @param number $code 错误码
	 * @param string $message 错误信息
	 * @throws NotifyException
	 * @return void
	 */
	protected final function throwNotifyException($code, $message) {
		throw new NotifyException($message, $code);
	}

	/**
	 * 抛出进行跳转的异常
	 * @param number $code 错误码
	 * @param string $message 错误信息
	 * @throws RedirectException
	 * @return void
	 */
	protected final function throwRedirectException($code, $message) {
		throw new RedirectException($message, $code);
	}

	/**
	 * 抛出表单数据的异常
	 * @param int $code 错误码
	 * @param array $message 错误信息
	 * @throws FormException
	 * @return void
	 */
	protected final function throwFormException($code, array $message) {
		throw new FormException($message, $code);
	}

	/**
	 * 获取完整url路径
	 * @param string $encode 是否进行编码，默认编码
	 * @return string
	 */
	protected function getFullUrl($encode = TRUE) {
		$request = $this->getRequest();
		$url = "{$request->getServer('REQUEST_SCHEME', 'http')}://{$request->getServer('SERVER_NAME')}{$request->getServer('REQUEST_URI')}";
		return $encode ? urlencode($url) : $url;
	}
}