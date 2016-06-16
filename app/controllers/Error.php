<?php

/**
 * 错误异常处理控制器
 * @author enychen
 */
class ErrorController extends \base\BaseController {

	/**
	 * 公共错误处理方法
	 * @return boolean
	 */
	public function errorAction() {
		$exception = $this->getRequest()->getException();
		switch(get_class($exception)) {
			case 'service\Exception\FormException':
				// 数据错误
				$this->showFormError($exception);
				break;
			case 'service\Exceptions\NotifyException':
				// 通知错误
				$this->showNotify($exception);
				break;
			case 'service\Exceptions\RedirectException':
				// 进行url跳转
				$this->redirect($exception->getMessage(), 'get');
				break;
			default:
				// 捕捉其他错误，一般是系统错误
				$this->systemError($exception);
				break;
		}
	}

	/**
	 * 显示提示
	 * @param \Exception $exception 异常对象
	 * @return void
	 */
	private function showNotify($exception) {
		if(IS_AJAX) {
			$this->jsonp($exception->getMessage(), 1001+$exception->getCode());
		} else {
			$this->notify($exception->getMessage(), 'notify');
		}

		exit();
	}

	/**
	 * 系统错误处理
	 * @param \Exception $exception 异常对象
	 * @return void
	 */
	private function systemError($exception) {
		// 整理数据
		$errorInfo['method'] = $_SERVER['REQUEST_METHOD'];
		$errorInfo['params'] = $_REQUEST; 
		$errorInfo['env'] = \Yaf\ENVIRON == 'product';
		$errorInfo['code'] = $exception->getCode();
		$errorInfo['file'] = $exception->getFile();
		$errorInfo['message'] = $exception->getMessage();
		$errorInfo['line'] = $exception->getLine();
		$errorInfo['traceAsString'] = $exception->getTraceAsString();

		// 线上环境
		if(\Yaf\ENVIRON == 'product') {
			// 线上环境报错
			$errorInfo['message'] = '服务器出错了，请稍后重试';
		}
		

		// 结果输出
		IS_AJAX ? $this->jsonp($errorInfo, 1003) : $this->notify($errorInfo, '502');
		exit();
	}
}