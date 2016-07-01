<?php

/**
 * 错误异常处理控制器
 * @author enychen
 */
class ErrorController extends \base\BaseController {

	/**
	 * 公共错误处理方法
	 * @param \Exception $exception 异常对象
	 * @return void
	 */
	public function errorAction($exception) {
		switch(get_class($exception)) {
			case 'traits\FormException':
				// 表单异常
				$this->showFormException($exception);
				break;
			case 'traits\NotifyException':
				// 通知错误
				$this->showNotifyException($exception);
				break;
			case 'traits\RedirectException':
				// 302页面跳转
				$this->showRedirectException($exception);
				break;
			case 'traits\ForbiddenException':
				// 403禁止访问
				$this->showForbiddenException($exception);
				break;
			case 'traits\NotFoundException':
			case 'Yaf\Exception\LoadFailed\Controller':
				// 404找不到资源
				$this->showNotFoundException($exception);
				break;
			default:
				// 捕捉系统异常
				$this->systemException($exception);
				break;
		}
	}

	/**
	 * 表单错误处理
	 * @param \traits\FormException $exception 表单异常对象
	 */
	private function showFormException(\traits\FormException $exception) {
		$error = $exception->getError();
		echo '<pre>';
		print_r($error);
		exit;
		$this->template('form');
	}

	/**
	 * 显示提示
	 * @param \Exception $exception 异常对象
	 * @return void
	 */
	private function showNotifyException($exception) {
		$this->assign('error', $exception->getMessage());
		$this->template('notify');
	}

	/**
	 * 错误跳转处理
	 * @param unknown $exception
	 */
	private function showRedirectException($exception) {
		$this->redirect($exception->getMessage());
	}

	/**
	 * 禁止访问操作(403)
	 */
	private function showForbiddenException($exception) {
		$this->template('403');
	}

	/**
	 * 找不到页面操作(402)
	 */
	private function showNotFoundException($exception) {
		$this->template('404');
	}

	/**
	 * 系统错误处理
	 * @param \Exception $exception 异常对象
	 * @return void
	 */
	private function systemException(\Exception $exception) {
		// 线上环境
		if(\Yaf\ENVIRON == 'product') {
			$errorInfo = array('message'=>'502 Server Error');
		} else {
			$errorInfo['method'] = $_SERVER['REQUEST_METHOD'];
			$errorInfo['params'] = $_REQUEST;
			$errorInfo['env'] = \Yaf\ENVIRON == 'product';
			$errorInfo['code'] = $exception->getCode();
			$errorInfo['file'] = $exception->getFile();
			$errorInfo['message'] = $exception->getMessage();
			$errorInfo['line'] = $exception->getLine();
			$errorInfo['traceAsString'] = $exception->getTraceAsString();
		}
		
		// 保存信息
		$this->assign('error', $errorInfo);
		$this->template('502');
	}
}