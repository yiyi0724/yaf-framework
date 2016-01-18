<?php

/**
 * 控制基类
 */
namespace Base;

use \Yaf\Application;
use \Yaf\Controller_Abstract;
use \Yaf\Session;
use \Security\Validate;

abstract class BaseController extends Controller_Abstract
{

	/**
	 * 需要登录的控制器方法
	 * @var array
	 */
	protected $loginAction = array();

	/**
	 * 控制器初始化
	 */
	public function init()
	{
		// 初始化用户UID
		define('UID', Session::getInstance()->get('user.uid'));
		
		// 初始化静态地址url
		$this->resource();
		
		// 登录检查
		$this->needLogin();
	}

	/**
	 * 静态资源常量定义
	 */
	protected function resource()
	{
		foreach($this->getConfig('resource') as $key=>$resource)
		{
			$key = 'URL_' . strtoupper($key);
			define($key, $resource);
		}
	}

	/**
	 * 登录检查
	 */
	protected function needLogin()
	{
		$request = $this->getRequest();
		
		// 读取方法名
		$action = $request->getActionName();
		// 是否需要优先登录
		if(!UID && ($this->loginAction == '*' || in_array($action, $this->loginAction)))
		{
			$url = '/member/login';
			if($request->isXmlHttpRequest())
			{
				$this->json(FALSE, array('alert'=>'请先登录', 'location'=>$url), 'alert-location');
			}
			else
			{
				$this->location($url);
			}
		}
	}

	/**
	 * 数据合法性检查
	 */
	protected function validate()
	{
		$request = $this->getRequest();
		
		// 读取校验文件
		$path = $this->getConfig('application.directory');
		$controller = $request->getControllerName();
		$action = $request->getActionName();
		$file = strtolower("{$path}validates/{$controller}/{$action}.json");
		
		try
		{
			$data = Validate::validity($file);
		}
		catch(\Exception $e)
		{
			if($request->isXmlHttpRequest())
			{
				$this->json(FALSE, '表单参数有误', array(), $data);
			}
			else
			{
				$this->getView()->display('common/error.phtml', $data);
				exit();
			}
		}
	}

	/**
	 * 加载本控制器下的视图
	 * @param array $data 输出到页面的数据, 格式: array('key'=>'value')
	 * @param string $template 自定义模板
	 */
	protected function view(array $vars, $tpl = NULL)
	{
		// 数据绑定
		$view = $this->getView();
		foreach($vars as $key=>$value)
		{
			$view->assign($key, $value);
		}
		
		// 加载自定义视图
		$tpl and $this->disView() and $this->display($tpl);
	}

	/**
	 * 输出json或者jsonp数据
	 * @param bool $status 结果状态
	 * @param string $notify　提示信息或者跳转
	 * @param string $action 前端处理方式 append-数据dom操作 | alert-输出$notify提示 | location-输出提示后跳转 | form-参数有误 | alert-location
	 */
	protected function json($status = FALSE, $notify = NULL, $action = 'alert')
	{
		// 关闭视图
		$this->disView();
		
		// 数据
		$output['status'] = $status;
		$output[$action] = $notify;
		
		// jsonp回调函数, 检查函数名
		$jsonp = $this->getRequest()->get('jsonp', NULL);
		if(preg_match('/^[a-zA-Z_][a-zA-Z0-9_\.]*$/', $jsonp))
		{
			$jsonp = NULL;
		}
		
		// 格式化json
		$output = json_encode($output);
		$header = 'application/json';
		
		// 如果是jsonp
		if($jsonp)
		{
			$header = 'text/javascript';
			$output = "<script type='text/javascript'>{$jsonp}({$json});</script>";
		}
		
		// 结果输出
		header("Content-type: {$header}; charset=UTF-8");
		exit($output);
	}

	/**
	 * 地址跳转
	 * @param string $url 跳转地址
	 * @param int $time 等待几秒后跳转
	 */
	protected function location($url, $time = 0)
	{
		exit("<meta http-equiv=\"refresh\" content=\"{$time};url={$url}\">");
	}

	/**
	 * 读取配置
	 * @param string $key 键名
	 * @return string | array
	 */
	protected function getConfig($key)
	{
		$config = Application::app()->getConfig()->get($key);
		return is_string($config) ? $config : $config->toArray();
	}

	/**
	 * 关闭默认视图
	 */
	protected function disView()
	{
		return Application::app()->getDispatcher()->disableView();
	}
}