<?php

/**
 * 错误异常处理控制器
 * @author enychen
 */
use \network\Header;

class ErrorController extends BaseController {

	public function init() {
		// 使用默认空模板
		$this->getView()->getTemplate()->setLayout('empty');
	}

	/**
	 * 公共错误处理方法
	 * @param \Exception $exception 异常对象
	 * @return void
	 */
	public function errorAction($exception) {
		switch(get_class($exception)) {
			case 'Traits\FormException':
				// 表单异常
				$this->showFormException($exception);
				break;
			case 'Exception\NotifyException':
				// 通知错误
				$this->showNotifyException($exception);
				break;
			case 'Traits\RedirectException':
				// 302页面跳转
				$this->showRedirectException($exception);
				break;
			case 'Traits\ForbiddenException':
				// 403禁止访问
				$this->showForbiddenException($exception);
				break;
			case 'Traits\NotFoundException':
			case 'Yaf\Exception\LoadFailed\Action':
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
		switch(TRUE) {
			case IS_AJAX:
				$this->json(FALSE, '表单数据有误', 991, $exception->getError());
				break;
			default:
				$this->assign('error', $exception->getError());
				$this->display('form');
		}
	}

	/**
	 * 显示提示
	 * @param \Exception $exception 异常对象
	 * @return void
	 */
	private function showNotifyException($exception) {
		switch(TRUE) {
			case IS_AJAX:
				$this->json(FALSE, $exception->getMessage(), $exception->getCode());
				break;
			default:
				$this->assign('error', $exception->getMessage());
				$this->display('notify');
		}
	}

	/**
	 * 错误跳转处理
	 * @param unknown $exception
	 */
	private function showRedirectException($exception) {
		switch(TRUE) {
			case IS_AJAX:
				$this->json(FALSE, '进行跳转', array('url'=>$exception->getMessage()));
				break;
			default:
				// 进行跳转
				\network\Redirect::getWithoutReferer($exception->getMessage());
				exit;
		}
	}

	/**
	 * 禁止访问操作(403)
	 */
	private function showForbiddenException($exception) {
        Header::forbidden();
		exit;
	}

	/**
	 * 找不到页面操作(404)
	 */
	private function showNotFoundException($exception) {
		switch(TRUE) {
			case IS_AJAX:
                Header::notFound();
				exit;
			default:
				$this->display('404');
		}
	}

	/**
	 * 系统错误处理
	 * @param \Exception $exception 异常对象
	 * @return void
	 */
	private function systemException(\Exception $exception) {
		if(\Yaf\ENVIRON == 'product') {
			// 线上环境
			$error = NULL;
			$message = '服务器出问题了';
			// 日志记录
			error_log(sprintf($exception));
		} else {
			// 开发环境
			$message = $exception->getMessage();
			$error['env'] = \Yaf\ENVIRON == 'product';
			$error['code'] = $exception->getCode();
			$error['file'] = $exception->getFile();
			$error['line'] = $exception->getLine();
			$error['traceAsString'] = $exception->getTraceAsString();
		}
		
		switch(TRUE) {
			case IS_AJAX:
				$this->json(FALSE, $message, $error);
				break;
			default:
				$this->assign('message', $message);
				$this->assign('error', $error);
				$this->display('502');
		}
	}
}