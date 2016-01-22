<?php

/**
 * 控制基类
 */
use \Yaf\Application;
use \Yaf\Controller_Abstract;
use \Yaf\Session;
use \Yaf\Registry;
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
		// url定义
		foreach($this->getConfig('resource') as $key=>$resource)
		{
			$key = 'URL_' . strtoupper($key);
			define($key, $resource);
		}
		
		// 请求方式定义
		define('IS_AJAX', $this->getRequest()->isXmlHttpRequest());
		define('IS_GET', $this->getRequest()->isGet());
		define('IS_POST', $this->getRequest()->isPost());
		define('IS_PUT', $this->getRequest()->isPut());
		define('IS_DELETE', $_SERVER['REQUEST_METHOD'] == 'DELETE');
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
		
		// 数据校验
		try
		{
			$input = Validate::validity($file);
		}
		catch(\Security\FormException $e)
		{
			$error = $e->getMessage();
			IS_AJAX ? $this->output(['form'=>$error], FALSE, 90001) : $this->template(['notify'=>$error], 
				'common/error.phtml');
		}
		
		return $input;
	}

	/**
	 * 加载本控制器下的视图
	 * 就是view/CONTROLLER_NAME/$template.phtml
	 * @param string $template 自定义模板
	 */
	protected function template(array $output, $tpl = NULL)
	{
		// 模板替换
		$tpl and $this->disView() and $this->display($tpl);
		
		// 加载页面输出数据
		$view = $this->getView();
		foreach($output as $key=>$value)
		{
			$view->assign($key, $value);
		}
		exit();
	}

	/**
	 * 数据输出
	 * @param array $output 要输出的数据
	 */
	public function jsonp(array $output, $status = TRUE, $code = NULL)
	{
		$request = $this->getRequest();
		
		// ajax请求
		if($request->isXmlHttpRequest())
		{
			// 关闭视图
			$this->disView();
			
			// 数据整理
			$json['data'] = $output;
			$json['status'] = $status;
			$json['code'] = $code;
			
			// jsonp回调函数, 检查函数名
			$jsonp = $request->get('callback', NULL);
			if(preg_match('/^[a-zA-Z_][a-zA-Z0-9_\.]*$/', $jsonp))
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
				$output = "<script type='text/javascript'>{$jsonp}({$json});</script>";
			}
			
			// 结果输出
			header("Content-type: {$header}; charset=UTF-8");
			exit($json);
		}
	}

	/**
	 * 地址跳转
	 * @param string $url 跳转地址
	 * @param int $time 等待几秒后跳转
	 */
	protected function location($url, $time = 0)
	{
		\Network\Location::post($url, ['name'=>'eny']);
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