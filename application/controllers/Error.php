<?php


class ErrorController extends BaseController
{
	public function init(){}
		
	public function errorAction()
	{
		// 获取异常对象
		$exception = $this->getRequest()->getException();

		// 没有异常对象表示直接访问此控制器，跳转到首页
		!$exception and $this->location('/');
		
		// 判断是否是线上环境
		$errorInfo = array('env'=>(\Yaf\ENVIRON != 'product'), 'message'=>'服务器离家出走了');
		if(\Yaf\ENVIRON != 'product')
		{
			$errorInfo['code'] = $exception->getCode();
			$errorInfo['file'] = $exception->getFile();
			$errorInfo['message'] = $exception->getMessage();
			$errorInfo['line'] = $exception->getLine();
			$errorInfo['traceAsString'] = $exception->getTraceAsString();
		}
		
		IS_AJAX ? $this->jsonp($errorInfo, FALSE, 90000) : $this->template(['error'=>$errorInfo]);		
	}
}