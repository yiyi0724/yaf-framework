<?php

use Yaf\Controller_Abstract;
use Yaf\Dispatcher;
use Yaf\Registry;
use Security\Input;
use Network\Header;

abstract class BaseController extends Controller_Abstract
{
	/**
	 * 请求对象
	 * @var Yaf\Request_Abstract
	 */
	protected $request;
	
	/**
	 * 初始化内容
	 */
	public function init()
	{
		// 获取用户id
		define('UID', isset($_SESSION['user']['uid']) ? $_SESSION['user']['uid'] : NULL);
		
		// 初始化静态文件url
	}
	
	protected function validate()
	{
		// 数据检查
		$request = $this->getRequest();
		
		// 数据检查
		$controller = $request->getControllerName();
		$action = $request->getActionName();
		$validatePath = $this->getConfig('validate.directory');
		$filename = strtolower("{$validatePath}{$controller}/{$action}.php");
		Validate::process($filename);
	}
	
	/**
	 * 获取配置信息
	 * @param array $key
	 * @return mixed
	 */
	protected function getConfig($key)
	{
		return Registry::get('config')->get($key);
	}
	
	protected function jsonp($data=array(), $status=200, $message=null, $callback=null)
	{
		// 关闭视图
		$this->disableView();
		
		// 输出数据
		$jsonArr = array();
		$jsonArr['status'] = $status;
		$jsonArr['message'] = $errmsg;
		$jsonArr['data'] = $data;
		$jsonArr = json_encode($jsonArr);
		
		// 为了安全 callback 只允许 字母+数字+_ 的组合
		if(!preg_match('/^[a-zA-Z_][a-zA-Z0-9_\.]*$/', $callback)) {
			$callback = null;
		}
		
		if($callback)
		{
			// 输出文件头信息
			Header::contentType('text/javascript');
			// 输出JSONP
			echo "<script type=\"text/javascript\">{$callback}({$jsonArr});</script>";
		} 
		else
		{
			// 输出文件头信息
			Header::contentType('application/json');
			// 输出JSON
			$this->getResponse()->setBody($jsonArr);
		}
		
		return true;
	}
	
	/**
	 * 关闭视图
	 * @return boolean
	 */
	protected function disableView()
	{
		Dispatcher::getInstance()->disableView();
	}
	
	/**
	 * js获取验证规则
	 */
	public function getRuleAction()
	{
		
	}
}