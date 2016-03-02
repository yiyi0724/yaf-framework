<?php


/**
 * 错误异常处理控制器
 * @author enychen
 */
class ErrorController extends \Base\BaseController
{	
	public function errorAction()
	{
		// 获取异常
		$exception = $this->getRequest()->getException();
		
		// 整理数据
		$errorInfo['env'] = \Yaf\ENVIRON == 'product';
		$errorInfo['code'] = $exception->getCode();
		$errorInfo['file'] = $exception->getFile();
		$errorInfo['message'] = $exception->getMessage();
		$errorInfo['line'] = $exception->getLine();
		$errorInfo['traceAsString'] = $exception->getTraceAsString();
		
		// 线上环境
		if($errorInfo['env'])
		{
			// 封装数据
			$errorInfo['data']['from'] = $_SERVER['REQUEST_METHOD'];
			$errorInfo['data']['list'] = $this->getRequest()->getParams();
			
			// 日志记录
			\File\Log::record($errorInfo, DATA_PATH);

			// 线上环境报错
			$errorInfo = '服务器出错了，请稍后重试';
		}
		
		// ajax返回json
		IS_AJAX and $this->jsonp($errorInfo, 502);

		// 模板加载
		$viewObject = $this->getView();
		$viewObject->assign('error', $errorInfo);
		$viewObject->setScriptPath(MODULE_PATH . 'views');
		$viewObject->display('common/error.phtml');
		
		// 结束执行
		exit();
	}
}