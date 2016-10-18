<?php

/**
 * 表单检查类
 * @author enychen
 */
namespace Traits;

use \Tool\Is;
use \Exceptions\MultiException;

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
	protected $method = 'GET';

	/**
	 * 参数正确列表
	 * @var array
	 */
	protected $success = array();

	/**
	 * 参数错误列表
	 * @var array
	 */
	protected $error = array();

	/**
	 * 构造函数
	 * @param array $params 参数列表
	 * @param string $method 请求方式的检查
	 */
	public function __construct(array $params, $method = NULL) {
		$this->setParams($params)->setMethod($method);
	}

	/**
	 * 设置参数列表
	 * @param array  $params 输入数据
	 * @return Form $this 返回当前对象进行连贯操作
	 */
	protected function setParams(array $params) {
		$this->params = $params;
		return $this;
	}

	/**
	 * 获取参数列表的某一个值
	 * @param string $key 键名
	 * @param string $default 默认值
	 * @return mixed
	 */
	protected function getParams($key, $default = NULL) {
		return isset($this->params[$key]) ? $this->params[$key] : $default;
	}

	/**
	 * 设置规则
	 * @param array $rules
	 * @return Form $this 返回当前对象进行连贯操作
	 */
	protected function setRules(array $rules) {
		foreach($rules as $index=>&$rule) {
			// 参数过滤
			if($rule['from'] != $this->getMethod()) {
				unset($rules[$index]);
				continue;
			}

			// 整合待检查的值
			$value = $this->getParams($rule['name']);
			if(is_null($value) && isset($rule['default'])) {
				$value = $rule['default'];
				unset($rule['default']);
			}
			$rule['value'] = trim($value);

			// 别名整合
			$rule['origin'] = $rule['name'];
			if(isset($rule['alias'])) {
				$rule['origin'] = $rule['name'];
				$rule['name'] = $rule['alias'];
				unset($rule['alias']);
			}
	
			// 扩展整理
			$rule['options'] = array();
			$options = array('min', 'max', 'regular', 'range', 'xss', 'exists');
			foreach($options as $key) {
				if(empty($rule[$key])) {
					continue;
				}

				if($key == 'exists') {
					$rule['options'] = explode(',', $rule[$key]);
					unset($rule[$key]);
					break;
				} else {
					$rule['options'][$key] = $rule[$key];
					unset($rule[$key]);
				}
			}
		}

		$this->rules = $rules;
		return $this;
	}

	/**
	 * 获取规则信息
	 * @return array
	 */
	public function getRules() {
		return $this->rules;
	}

	/**
	 * 设置请求方式
	 * @param array $method 请求方式：GET | POST | DELETE | PUT
	 * @return Form $this 返回当前对象进行连贯操作
	 */
	public function setMethod($method) {
		$this->method = strtolower($method);
		return $this;
	}

	/**
	 * 获取请求方式
	 * @return string
	 */
	public function getMethod() {
		return $this->method;
	}

	/**
	 * 设置检查不通过的参数信息
	 * @param array $rule 规则数组
	 * @return Form $this 返回当前对象进行连贯操作
	 */
	protected function setError($rule) {
		$this->error[$rule['origin']] = $rule['error'];
		return $this;
	}

	/**
	 * 获取检查不通过的参数信息
	 * @return array 失败的数组
	 */
	public function getError() {
		return $this->error;
	}

	/**
	 * 设置检查通过的参数信息
	 * @param array $rule 规则数组
	 * @return Form $this 返回当前对象进行连贯操作
	 */
	protected function setSuccess($rule) {
		if(!is_null($rule['value'])) {
			$this->success[$rule['name']] = $rule['value'];
		}

		return $this;
	}

	/**
	 * 获取检查通过的参数信息
	 * @return array
	 */
	public function getSuccess() {
		return $this->success;
	}

	/**
	 * 使用xml的规则方式
	 * @param \SimpleXMLElement $rules xml对象
	 * @return Form $this 返回当前对象进行连贯操作
	 */
	public function useXmlRule(\SimpleXMLElement $rules) {
		$temp = json_decode(json_encode($rules), TRUE);
		$rules = array();
		if(isset($temp['input'])) {
			foreach($temp['input'] as $key=>$value) {
				$rules[] = is_numeric($key) ? $value['@attributes'] : $value;
			}
		}
		$this->setRules($rules);
		return $this;
	}

	/**
	 * 检查过滤数据
	 * @return void
	 */
	public function fliter() {
		// 进行检查
		foreach($this->getRules() as $key=>$rule) {
			$method = $rule['type'];
			if($rule['require'] && is_null($rule['value'])) {
				// 是否必须检查
				$this->setError($rule);
			} else if(!is_null($rule['value']) && !Is::$method($rule['value'], $rule['options'])) {
				// 检查不通过
				$this->setError($rule);
			} else {
				$this->setSuccess($rule);
			}
		}

		// 数据有问题
		if($errors = $this->getError()) {
			throw new MultiException($errors);
		}
	}
}