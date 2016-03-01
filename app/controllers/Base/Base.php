<?php

namespace Base;

/**
 * 所有模块控制基类的基类
 */
use \Yaf\Controller_Abstract;
use \Yaf\Application;
use \Security\Validate;
use \Network\Location;
use \Yaf\Registry;

abstract class BaseController extends Controller_Abstract
{
	
	public function init()
	{
		// 保存当前对象
		Registry::set('view', 11111);
	}

	/**
	 * 数据检查并且返回成功数组
	 */
	protected function validity()
	{
		// 读取校验文件
		require (MODULE_PATH . 'validates/' . CONTROLLER . 'Form.php');
		list($controller, $action) = array(CONTROLLER . 'Form', ACTION . 'Rules');
		list($rules, $inputs) = array($controller::$action(), $this->getRequest()->getParams());
		list($success, $fail) = Validate::validity($rules, $inputs);
		if($fail)
		{
			// ajax输出
			IS_AJAX and $this->jsonp($fail, 412);
			// 页面输出
			$this->view(['form'=>$fail], 'common/notify', TRUE);
			exit();
		}
		
		return $success;
	}

	/**
	 * 加载模板
	 * @param array $output 参数绑定
	 * @param string $template 自定义模板
	 * @param bool $useView 是否使用通用模板
	 */
	protected function view(array $output, $template = NULL, $useView = FALSE)
	{
		// 数据绑定
		$view = $this->getView();
		$view->setScriptPath(MODULE_PATH.'views');
		foreach($output as $key=>$value)
		{
			$view->assign($key, $value);
		}
		
		// 模板替换
		$template and Application::app()->getDispatcher()->disableView();
		($template && $useView) ? $view->display("{$template}.phtml") : $this->display($template);
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
		
		// jsonp回调函数, 检查函数名
		$jsonp = $this->getRequest()->get('callback', NULL);
		if(!preg_match('/^[a-zA-Z_][a-zA-Z0-9_\.]*$/', $jsonp))
		{
			$jsonp = NULL;
		}
		
		// 格式化json
		$json = json_encode($json);
		$header = 'application/json';
		
		// 如果是jsonp
		if($jsonp)
		{
			$header = 'text/javascript';
			$output = "{$jsonp}({$json})";
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
		if(IS_AJAX)
		{
			$this->jsonp($url, 302);
		}
		else
		{
			exit(Location::$method($url, $data));
		}
	}
}