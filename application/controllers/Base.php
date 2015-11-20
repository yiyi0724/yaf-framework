<?php

use Yaf\Controller_Abstract;
use Yaf\Dispatcher;
use Yaf\Registry;
use Security\Validate;

abstract class BaseController extends Controller_Abstract
{	
	/**
	 * 初始化内容
	 */
	public function init()
	{
		// 定义UID常量
		$this->initUid();
		
		// 来源检查
		$this->validate();
	}
	
	/**
	 * 定义UID
	 */
	protected function initUid()
	{
		// 获取用户id
		define('UID', isset($_SESSION['user']['uid']) ? $_SESSION['user']['uid'] : NULL);
	}
	
	/**
	 * 数据检查
	 */
	protected function validate()
	{
		// 数据校验文件名
		$request = $this->getRequest();
		$controller = $request->getControllerName();
		$action = $request->getActionName();
		$fileName = APPLICATION_PATH.'/data/validate'.strtolower("/{$controller}/$action.json");
		
		// 数据校验
		$validate = new Validate($fileName);
		$validate->check();
		if($error = $validate->getError())
		{
			$this->jsonp($error, false, '数据校验失败');
		}
	}
	
	/**
	 * 输出json或者jsonp
	 * @param array $data
	 * @param boolean $status
	 * @param string $message
	 * @param string $callback
	 */
	protected function jsonp($data=array(), $status=true, $message=null, $callback=null)
	{
		// 输出数据
		$jsonArr = array();
		$jsonArr['status'] = $status;
		$jsonArr['message'] = $message;
		$jsonArr['data'] = $data;
		$response = json_encode($jsonArr);
		$header = 'application/json';

		// jsonp输出
		if(preg_match('/^[a-zA-Z_][a-zA-Z0-9_\.]*$/', $callback))
		{
			$header = 'text/javascript';
			$response = "<script type=\"text/javascript\">{$callback}({$jsonArr});</script>";
		}

		// 输出
		header("Content-Type:{$header};charset=UTF-8");
		echo $response;
		
		exit;
	}
	
	/**
	 * 关闭视图
	 * @return boolean
	 */
	protected function disableView()
	{
		Dispatcher::getInstance()->disableView();
	}
}