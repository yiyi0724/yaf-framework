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
		// 获取异常
		$exception = $this->getRequest()->getException();
		
		$class = get_class($exception);
		
		// 整理数据
		$errorInfo['env'] = \Yaf\ENVIRON == 'product';
		$errorInfo['code'] = $exception->getCode();
		$errorInfo['file'] = $exception->getFile();
		$errorInfo['message'] = $exception->getMessage();
		$errorInfo['line'] = $exception->getLine();
		$errorInfo['traceAsString'] = $exception->getTraceAsString();
		
		echo '<pre>';
		print_r($errorInfo);
		exit;
		
		switch($class) {
			case 'service\Exceptions\Notify':				
				// 通知错误
				$this->notify();
				break;
			case 'service\Exceptions\Redirect':
				$this->location();
				break;
			default:
				// 捕捉系统的错误
				$this->error();
				if(\Yaf\ENVIRON == 'product') {
					error_log(print_r($errorInfo, TRUE));
				}
				break;
		}
		
		// 线上环境
		if($errorInfo['env']) {
			// 封装数据
			$errorInfo['data']['from'] = $_SERVER['REQUEST_METHOD'];
			$errorInfo['data']['list'] = $this->getRequest()->getParams();
			
			// 日志记录
			\File\Log::record($errorInfo, DATA_PATH);
			
			// 线上环境报错
			$errorInfo = '服务器出错了，请稍后重试';
		}
		
		if(IS_AJAX) {
			$this->jsonp($errorInfo, 502);
		} else {
			$this->notify($errorInfo, 'error');
		}
		
		return FALSE;
	}
}