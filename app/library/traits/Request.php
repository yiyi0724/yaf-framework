<?php

namespace traits;

use \Yaf\Application;

class Request {

	/**
	 * 单例对象
	 * @var \traits\Request
	 */
	protected static $instance;

	/**
	 * 传递参数列表
	 * @var array
	 */
	protected $params = array();

	/**
	 * 禁止单例对象
	 * @return void
	 */
	protected final function __construct() {
	}

	/**
	 * 禁止克隆对象
	 * @return void
	 */
	protected final function __clone() {
	}

	/**
	 * 参数整合，清空全局变量，进行数据校验
	 * @param \Yaf\Request_Abstract $request 请求对象
	 * @param array $putOrDelete put和delete方法支持存放数组
	 * @return void
	 */
	public static function getInstance($putOrDelete = array()) {
		if(!self::$instance instanceof self) {
			// 创建单例对象
			self::$instance = new self();
			
			// PUT和DETELE方法支持
			if(IS_PUT || IS_DELETE) {
				parse_str(file_get_contents('php://input'), $putOrDelete);
			}
			
			// 输入数据源
			$request = Application::app()->getDispatcher()->getRequest();
			$params = array_merge($request->getParams(), $putOrDelete, $_REQUEST);
			
			// 获取检查规则
			$controller = sprintf('%sForm', CONTROLLER_NAME);
			$action = sprintf('%sAction', ACTION_NAME);
			$validFile = sprintf('%sforms%s%s.php', MODULE_PATH, DS, $controller);
			
			if(is_file($validFile)) {
				require ($validFile);
				if(method_exists($controller, $action)) {
					$rules = $controller::$action();
					$fromTrait = new Form();
					$fromTrait->setRequestMethod($request->getMethod());
					$fromTrait->setRules($rules);
					$fromTrait->setParams($params);
					if($errors = $fromTrait->fliter()) {
						throw new FormException($errors);
					}
					$params = $fromTrait->getSuccess();
				}
			}

			self::$instance->setParams($params);
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
	 * @param string $default 获取不到的时候，返回的默认值
	 * @return mixed
	 */
	public function get($key, $default = NULL) {
		return empty($this->params[$key]) ? $default : $this->params[$key];
	}
}