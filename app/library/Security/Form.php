<?php

/**
 * 表单检查类
 * @author enychen
 */
namespace Security;

class Form {

	/**
	 * 检查规则数组
	 * @var array
	 */
	protected $rules = array();

	/**
	 * 传递的参数数组
	 * @var array
	 */
	protected $params = array();

	/**
	 * 请求方式
	 * @var string
	 */
	protected $requestMethod;

	/**
	 * 检查通过
	 * @static
	 * @var array
	 */
	protected $success = array();

	protected $error = array();

	/**
	 * 设置规则
	 * @param array $rules
	 */
	public function setRules(array $rules) {
		$this->rules = $rules;
	}

	/**
	 * 初始化规则
	 * @param array 输入数据
	 * @return array 规则数组
	 */
	public function setParams(array $params) {
		$this->params = $params;
	}

	/**
	 * 设置请求方式
	 * @param unknown $requestMethod
	 */
	public function setRequestMethod($requestMethod) {
		$this->requestMethod = $requestMethod;
	}

	/**
	 * 初始化参数
	 * @param array $rules 原始规则数组
	 * @return bool 是否有需要检查的数据
	 */
	protected function init($rules = array()) {
		foreach($this->rules as $key=>$rule) {
			if(strcasecmp($this->requestMethod, $rule[0])) {
				continue;
			}
			$rules[$key]['value'] = isset($this->params[$key]) ? $this->params[$key] : NULL;
			$rules[$key]['method'] = $rule[1];
			$rules[$key]['require'] = $rule[2];
			$rules[$key]['notify'] = $rule[3];
			$rules[$key]['options'] = isset($rule[4]) && is_array($rule[4]) ? $rule[4] : array();
			$rules[$key]['default'] = isset($rule[5]) ? $rule[5] : NULL;
			$rules[$key]['alias'] = isset($rule[6]) ? $rule[6] : NULL;
		}
		
		$this->rules = $rules;		
		return (bool)$rules;
	}

	/**
	 * 检查数据
	 * @param string $method 请求方式
	 * @return void
	 */
	public function fliter() {
		// 检查方式
		if(!$this->requestMethod) {
			throw new \Exception('Please set the request mode', 502);
		}

		// 是否需要检查参数
		if($this->init()) {
			foreach($this->rules as $key=>$rule) {
				// 对应数据类型检查
				$method = "is{$rule['method']}";
				if($rule['require'] && is_null($rule['value'])) {
					$this->setError($key, $rule['notify']);
				} else if(!is_null($rule['value']) && !FormRule::$method($rule['value'], $rule['options'])) {
					$this->setError($key, $rule['notify']);
				} else {
					$this->setSuccess($key, $rule);
				}
			}
		}
				
		return $this->getError();
	}

	/**
	 * 保存检查通过的值
	 * @param array $rule
	 */
	protected function setSuccess($key, $rule) {
		// 是否存在别名
		$key = $rule['alias'] ? $rule['alias'] : $key;
		
		// 是否填充默认值
		if(is_null($rule['value']) && $rule['default']) {
			$rule['value'] = $rule['default'];
		}
		
		if(!is_null($rule['value'])) {
			$this->success[$key] = trim($rule['value']);
		}
	}

	/**
	 * 保存检查不通过的值
	 * @param unknown $key
	 * @param unknown $rule
	 */
	protected function setError($key, $notify) {
		$this->error[$key] = $notify;
	}

	/**
	 * 验证通过的字段
	 * @return multitype:
	 */
	public function getSuccess() {
		return $this->success;
	}

	/**
	 * 验证失败的字段
	 */
	public function getError() {
		return $this->error;
	}
}