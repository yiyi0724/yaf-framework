<?php


class ErrorController extends \Base\BaseController
{
	public function init(){}
		
	public function errorAction()
	{
		// 获取异常对象
		$exception = $this->getRequest()->getException();

		// 没有异常对象表示直接访问此控制器，跳转到首页
		!$exception and $this->location('/');
		
		switch(get_class($exception))
		{
			case 'Security\FormException':
				// 表单错误
				$template = 'notify';
				$errorInfo = $exception->getMessage();
				break;
			default:
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
				// 默认模板
				$template = NULL;
		}
		IS_AJAX ? $this->jsonp($errorInfo, FALSE, 90000) : $this->view(['error'=>$errorInfo], $template);		
	}
}