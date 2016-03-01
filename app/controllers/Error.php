<?php


/**
 * 错误异常处理控制器
 * @author enychen
 */
class ErrorController extends \Base\BaseController
{	
	public function errorAction()
	{
		$e = $this->getRequest()->getException();
		
		// 判断是否是线上环境		
		$errorInfo['env'] = \Yaf\ENVIRON != 'product';
		$errorInfo['type'] = $type;
		$errorInfo['code'] = $code;
		$errorInfo['file'] = $file;
		$errorInfo['message'] = $message;
		$errorInfo['line'] = $line;
		$errorInfo['traceAsString'] = $trace;
		if(!$errorInfo['env'])
		{
			// 封装数据
			$errorInfo['data']['from'] = $_SERVER['REQUEST_METHOD'];
			$errorInfo['data']['list'] = $_REQUEST;
			// 日志记录
			file_put_contents(APPLICATION_PATH . 'data/' . date('Y-m-d_H') . 'log', print_r($errorInfo, TRUE));
			// 线上环境报错
			IS_AJAX and ($errorInfo = '服务器出错了，请重试后联系客服');
		}
		
		if(IS_AJAX)
		{
			$json['code'] = 502;
			$json['message'] = $errorInfo;
			exit(json_encode($json));
		}
		else
		{
			$controller = Registry::get('view');
			print_r($controller);
			exit;
		}
	}
}