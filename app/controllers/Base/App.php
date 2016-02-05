<?php

namespace Base;

/**
 * 控制基类
 */
use \Yaf\Application;
use \Yaf\Controller_Abstract;
use \Yaf\Session;
use \Yaf\Loader;
use \Security\Validate;
use \Network\Location;

abstract class AppController extends Controller_Abstract
{

	/**
	 * 控制器初始化
	 */
	public function init()
	{
		// 初始化用户状态
		$this->initUserInfo();
		
		// 静态资源常量定义
		$this->resource();
		
		// 默认状态变更
		$this->changeDefault();
	}

	/**
	 * 初始化用户状态
	 */
	protected function initUserInfo()
	{
		define('UID', Session::getInstance()->get('user.uid'));
	}

	/**
	 * 静态资源常量定义
	 */
	protected function resource()
	{
		$request = $this->getRequest();
		
		// URL常量定义
		foreach($this->getConfig('resource') as $key=>$resource)
		{
			define('URL_' . strtoupper($key), $resource);
		}
		
		// 请求方式定义
		define('IS_AJAX', $request->isXmlHttpRequest());
		define('IS_GET', $request->isGet());
		define('IS_POST', $request->isPost());
		define('IS_PUT', $request->isPut());
		define('IS_DELETE', $_SERVER['REQUEST_METHOD'] == 'DELETE');
		
		// 模块常量定义
		define('CONTROLLER_NAME', $request->getControllerName());
		define('ACTION_NAME', $request->getActionName());
		define('MODULES_NAME', $request->getModuleName());
		
		// 所有请求的参数
		$GLOBALS["_{$_SERVER['REQUEST_METHOD']}"] = $request->getParams();
	}

	/**
	 * 默认行为变更
	 */
	protected function changeDefault()
	{
		// ajax请求关闭模板，否则设置默认模板地址
		IS_AJAX ? $this->disView() : $this->getView()->setScriptPath(APPLICATION_PATH . 'modules/' . MODULES_NAME . '/views');
	}

	/**
	 * 登录检查,未登录跳转
	 * @param string $url 跳转地址
	 * @param string $method 跳转方式
	 */
	protected function login($url = "/member/login", $method = 'get')
	{
		UID ? NULL : (IS_AJAX ? $this->jsonp($url, 302) : Location::$method($url));
	}

	/**
	 * 数据合法性检查
	 */
	protected function validate()
	{
		try
		{
			// 读取校验文件
			$module = MODULES_NAME;
			$controller = CONTROLLER_NAME . 'Form';
			$action = ACTION_NAME . 'Rules';
			
			// 数据校验
			Loader::import(APPLICATION_PATH . "modules/{$module}/validates/{$controller}.php");
			$rules = $controller::$action();
			return Validate::validity($rules);
		}
		catch(\Security\FormException $e)
		{
			// ajax输出
			IS_AJAX and $this->jsonp([$rules[$e->getCode()][0]=>$e->getMessage()], 412);
			// 页面输出
			$this->view(['notify'=>$e->getMessage()], 'common/notify', TRUE);
			exit();
		}
	}

	/**
	 * 加载模板
	 * @param array $output 参数绑定
	 * @param string $template 自定义模板
	 * @param bool $useView 是否使用通用模板
	 */
	protected function view(array $output, $tpl = NULL, $useView = FALSE)
	{
		// 数据绑定
		$view = $this->getView();
		foreach($output as $key=>$value)
		{
			$view->assign($key, $value);
		}
		
		// 模板替换
		$tpl and $this->disView();
		($tpl && $useView) ? $view->display("{$tpl}.phtml") : $this->display($tpl);
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
			$output = "<script type=\"text/javascript\">{$jsonp}({$json});</script>";
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
		exit(Location::$method($url, $data));
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