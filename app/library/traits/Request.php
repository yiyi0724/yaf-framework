<?php

/**
 * 自定义请求对象
 * @author enychen
 */
namespace traits;

use \Yaf\Application;

class Request {

	/**
	 * 单例对象
	 * @var \traits\Request
	 */
	protected static $instance;

	/**
	 * yaf请求对象
	 * @var \Yaf\Request_Abstract
	 */
	protected $yafRequest = NULL;

	/**
	  参数列表
	 * @var array
	 */
	protected $params = array();

	/**
	 * 禁止单例对象
	 * @return void
	 */
	protected final function __construct() {
		// PUT和DETELE方法支持
		$putOrDelete = array();
		if(IS_PUT || IS_DELETE) {
			parse_str(file_get_contents('php://input'), $putOrDelete);
		}

		// 输入数据源
		$this->setYafRequest(Application::app()->getDispatcher()->getRequest());
		$params = array_merge($this->getYafRequest()->getParams(), $putOrDelete, $_REQUEST);

		// 获取检查规则
		$xml = sprintf('%sforms%s%s%s%s.xml', MODULE_PATH, DS, strtolower(CONTROLLER_NAME), DS, strtolower(ACTION_NAME));
		if(is_file($xml)) {
			// 读取xml文件
			$simpleXMLElements = @simplexml_load_file($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
			if(!$simpleXMLElements) {
				throw new \Exception("{$validFile}语法有误");
			}
			// 进行表单检查
			$fromTrait = new Form($params, $this->getYafRequest()->getMethod());
			$fromTrait->useXmlRule($simpleXMLElements)->fliter();
			$params = $fromTrait->getSuccess();
		}

		$this->setParams($params);
	}

	protected function setYafRequest() {
		$this->yafRequest = Application::app()->getDispatcher()->getRequest();
		return $this;
	}

	public function getYafRequest() {
		return $this->yafRequest;
	}

	/**
	 * 禁止克隆对象
	 * @return void
	 */
	protected final function __clone() {
	}

	/**
	 * 参数整合，清空全局变量，进行数据校验
	 * @return void
	 */
	public static function getInstance() {
		if(!self::$instance instanceof self) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * 设置所有的传递参数
	 * @param array $params 传递参数
	 * @return void
	 */
	protected function setParams($params) {
		$this->params = $params;
	}

	/**
	 * 获取所有的传递参数
	 * @return array
	 */
	public function getParams() {
		return $this->params;
	}

	/**
	 * 获取参数
	 * @param string $key 参数键
	 * @return mixed
	 */
	public function get($key) {
		return $this->params[$key];
	}

	/**
	 *  回调
	 * @param unknown $method
	 * @param unknown $args
	 */
	public function __call($method, $args) {
		return call_user_func_array(array($this->getYafRequest(), $method), $args);
	}
}