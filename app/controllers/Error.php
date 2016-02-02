<?php
/**
 * 错误异常处理控制器
 * @author enychen
 */
class ErrorController extends \Base\AppController
{
	/**
	 * 不要初始化
	 */
	public function init()
	{
	}

	/**
	 * 默认的错误异常处理
	 */
	public function errorAction()
	{
		// 获取异常对象
		$exception = $this->getRequest()->getException();
		
		// 没有异常对象表示直接访问此控制器，跳转到首页
		!$exception and \Network\Location::get('/');
		
		// 判断是否是线上环境
		$errorInfo['env'] = \Yaf\ENVIRON != 'product';
		$errorInfo['code'] = $exception->getCode();
		$errorInfo['file'] = $exception->getFile();
		$errorInfo['message'] = $exception->getMessage();
		$errorInfo['line'] = $exception->getLine();
		$errorInfo['traceAsString'] = $exception->getTraceAsString();
		if(!$errorInfo['env'])
		{
			// 封装数据
			$errorInfo['data']['from'] = $_SERVER['REQUEST_METHOD'];
			$errorInfo['data']['list'] = $_REQUEST;
			// 日志记录
		}
		
		$this->getRequest()->isXmlHttpRequest() and $this->jsonp($errorInfo, 504);
		
		$this->view(['error'=>$errorInfo], 'common/error', TRUE);
	}
}