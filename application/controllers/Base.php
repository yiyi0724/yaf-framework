<?php
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
	 * 传递的参数
	 * @var array
	 */
	protected $params = array();

	/**
	 * 控制器初始化
	 */
	public function init()
	{
		// 初始化用户UID
		define('UID', Session::getInstance()->get('user.uid'));
		
		// 初始化静态地址url
		$this->staticUrl();
		
		// 控制器登录检查
		$this->needLogin();
	}

	/**
	 * 初始化url地址
	 */
	protected function staticUrl()
	{
	}

	/**
	 * 需要先登录再操作的控制器
	 */
	protected function needLogin()
	{
		// 读取方法名
		$action = $this->getRequest()->getActionName();
		// 是否需要优先登录
		if(!UID && (in_array($action, $this->loginAction) || $this->loginAction == '*'))
		{
			$this->location('/member/login');
		}
	}

	/**
	 * 地址跳转
	 * @param string $url 跳转地址
	 */
	protected function location($url)
	{
		parent::redirect($url);
		exit();
	}

	/**
	 * 数据合法性检查
	 */
	protected function validate()
	{
		$request = $this->getRequest();
		
		// 读取校验文件
		$path = Application::app()->getConfig()->get('application.directory');
		$controller = $request->getControllerName();
		$action = $request->getActionName();
		$file = strtolower("{$path}validates/{$controller}/{$action}.json");
		
		// 数据检查
		list($data, $error) = Validate::validity($file);
		echo '<pre>';
		print_r($data);
		exit;
		if($error)
		{
			if($request->isXmlHttpRequest())
			{
				$this->json(FALSE, '表单参数有误', array(), $data);
			}
			else
			{
				$this->view($data, 'common/error');
			}
			exit();
		}
	}

	/**
	 * 关闭视图
	 * @param string $view
	 */
	protected function disView()
	{
		Application::app()->getDispatcher()->disableView();
	}

	/**
	 * 加载视图,绑定语法
	 * @param array $data 输出到页面的数据, 格式: array('key'=>'value')
	 * @param string $template 自定义模板
	 */
	protected function view(array $data, $template = NULL)
	{
		// 数据绑定
		$view = $this->getView();
		foreach($data as $key=>$value)
		{
			$view->assign($key, $value);
		}
		
		// 如果自定义视图,则关闭默认视图并加载自定义视图
		if($template)
		{
			$this->disView();
			$this->display($template);
		}
	}

	/**
	 * 输出json或者jsonp数据
	 * @param bool $status 结果状态
	 * @param string $notify　提示信息
	 * @param array　$data 参数列表
	 */
	protected function json($status = FALSE, $notify = NULL, array $success = array(), $error = array())
	{
		// 关闭视图
		$this->disView();
		
		// 数据
		// $output['action'] =
		$output['status'] = $status;
		$output['notify'] = $notify;
		$output['success'] = $success;
		$output['error'] = $error;
		
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
			$header = 'application/javascript';
			$output = "<script type='text/javascript'>{$jsonp}({$json});</script>";
		}
		
		// 结果输出
		header("Content-type: {$header}; charset=UTF-8");
		echo $output;
	}
}