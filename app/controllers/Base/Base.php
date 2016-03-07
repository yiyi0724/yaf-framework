<?php

namespace Base;

/**
 * 所有模块控制基类的基类
 */
use \Yaf\Controller_Abstract;
use \Yaf\Application;
use \Yaf\Session;
use \Security\Validate;
use \Network\Location;

abstract class BaseController extends Controller_Abstract
{

	/**
	 * 数据检查并且返回成功数组
	 */
	protected function validity()
	{
		$request = $this->getRequest();
		
		// 读取校验文件
		require (MODULE_PATH . 'validates/' . CONTROLLER . 'Form.php');
		list($controller, $action) = [CONTROLLER . 'Form', ACTION . 'Rules'];
		list($rules, $inputs) = [$controller::$action(), $this->getRequest()->getParams()];
		list($success, $fail) = Validate::validity($rules, $inputs);
		
		// 是否有误
		if($fail)
		{
			IS_AJAX ? $this->jsonp($fail, 412) : $this->view(['form'=>$fail], 'common/notify', TRUE);
		}
		
		// 更新全局变量
		foreach($request->getParams() as $source=>$param)
		{
			if(empty($success[$source]))
			{
				$request->setParam($source, NULL);
				continue;
			}
			
			// 更新参数
			$request->setParam($source, $success[$source]);
		}
		
		return $success;
	}

	/**
	 * 加载模板
	 * @param array $output 参数绑定
	 * @param string $template 自定义模板
	 * @param bool $useView 是否使用通用模板
	 */
	protected function template(array $output, $template = NULL)
	{
		// 数据绑定
		$view = $this->getView();
		foreach($output as $key=>$value)
		{
			$view->assign($key, $value);
		}
		
		// 模板替换
		if($template)
		{
			Application::app()->getDispatcher()->disableView();
			$this->display($template);
		}
		exit();
	}

	/**
	 * 数据输出
	 * @param array $output 要输出的数据
	 */
	public function jsonp($output, $code = 200)
	{
		// 数据整理
		$json['message'] = $output;
		$json['code'] = $code;
		$json = json_encode($json);
		
		// jsonp回调函数, 检查函数名
		if($jsonp = $this->getRequest()->get('callback', NULL))
		{
			$header = 'text/javascript';
			$json = "{$jsonp}({$json})";
		}
		else
		{
			$header = 'application/json';
		}
		
		// 结果输出
		header("Content-type: {$header}; charset=UTF-8");
		exit($json);
	}

	/**
	 * 页面跳转
	 * @param string $url 要跳转的url地址
	 * @param string $method 跳转方式，get | post |redirect
	 * @param array|int $data 如果是post请输入数组，如果是redirect请输入301|302|303|307	 
	 */
	protected function location($url, $method = 'get', $data = array())
	{
		IS_AJAX ? $this->jsonp($url, 302) : Location::$method($url, $data);
	}
	
	/**
	 * 获取session操作对象
	 */
	protected function getSession()
	{
		return Session::getInstance();
	}
}