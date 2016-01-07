<?php
use \Yaf\Application;
use \Yaf\Controller_Abstract;
use \Yaf\Session;
use \Yaf\Loader;
class BaseController extends Controller_Abstract
{

	/**
	 * 需要登录的控制器方法
	 * @var array
	 */
	protected $loginAction = array();

	/**
	 * 控制器对象
	 */
	protected $modules = array();

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
		
		// 数据和发现检查
		$this->validate();
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
		exit;
	}

	/**
	 * 数据合法性检查
	 */
	protected function validate()
	{
	}

	/**
	 * 获取某一个模块对象
	 * @param $array $class 模块名称
	 * @param \Module 某一个模块对象
	 */
	protected function getModule($class)
	{
		if(empty($this->modules[$class]))
		{
			$directory = $this->getConfig('application.directory');
			Loader::import($directory.'modules'.str_replace('\\', '/', $class).'.php');
			$class = "{$class}Module";
			$this->modules[$class] = new $class($this);
		}
		
		return $this->modules[$class];
	}

	/**
	 * 读取application.ini配置文件的信息
	 * @param string $key
	 */
	protected function getConfig($key)
	{
		$result = Application::app()->getConfig()->get($key);
		return is_string($result) ?  : $result->toArray();
	}

	/**
	 * 关闭视图
	 * @param string $view 用新页面
	 */
	protected function disView()
	{
		Application::app()->getDispatcher()->disableView();
	}

	/**
	 * 加载视图,绑定语法
	 * @param array $data 输出到页面的数据, 格式: array('key'=>'value')
	 * @param string $template 自定义模板,会取消默认的模板
	 */
	protected function view(array $data, $template = NULL)
	{
		// 如果自定义视图,则关闭默认视图
		$template and $this->disView();
		// 数据绑定
		$viewObject = $this->getView();
		foreach($data as $key=>$value)
		{
			$viewObject->assign($key, $value);
		}
	}

	/**
	 * 输出json数据
	 */
	protected function json($status = FALSE, $notify = NULL, array $success = array(), $failed = array())
	{
		// 关闭视图
		$this->disView();
		
		$json['status'] = $status;
		$json['success'] = $success;
		$json['failed'] = $failed;
		$json['notify'] = $notify;
		
		// 为了安全 callback 只允许 字母+数字+_ 的组合
		if(!preg_match('/^[a-zA-Z_][a-zA-Z0-9_\.]*$/', $callback))
		{
			$callback = null;
		}
		
		// 是否有 callback, 如果没有, 那么直接输出JSON
		if($callback)
		{
			// 输出JSONP			
			echo '<script type="text/javascript">' . $callback . '(' . json_encode($jsonArr) . ');</script>';
		}
		else
		{
			// 输出文件头信息
			header('Content-type: application/json; charset=utf-8');
			// 输出JSON
			echo json_encode($jsonArr);
		}
	}
}