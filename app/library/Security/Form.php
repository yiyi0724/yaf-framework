<?php

/**
 * 表单检查类&表单规则类
 * @author enychen
 */
namespace Security;

class Form {
	/**
	 * 检查通过
	 * @static
	 * @var array
	 */
	protected static $success = array();

	/**
	 * 检查失败
	 * @static
	 * @var array
	 */
	protected static $error = array();

	/**
	 * 检查数据
	 * @param array $rules 数据规则数组
	 * @param array $data 输入源数据
	 * @return void
	 */
	public static function fliter(array $rules, array $params) {
		if($rules = static::init($rules, $params)) {
			foreach($rules as $key=>$rule) {
				// 是否必须传递
				if(Rule::isNotExists($rule)) {
					static::setError($key, $rule);
					continue;
				}
				
				// 对应数据类型检查
				if(!is_null($rule['value']) && call_user_func("Rule::is{$rule['method']}", $rule)) {
					static::setError($key, $rule);
					continue;
				}
				
				// 设置合法值
				static::setSuccess($key, $rule);
			}
		}
	}

	/**
	 * 初始化规则
	 * @param array 规则数组
	 * @param array 输入源
	 * @return array 规则数组
	 */
	protected static function init($checks, $params) {
		foreach($checks as $key=>$check) {
			if($check[0] && strcasecmp($_SERVER['REQUEST_METHOD'], $check[0])) {
				continue;
			}
			$rules[$key]['value'] = isset($params[$key]) ? $params[$key] : NULL;
			$rules[$key]['method'] = $check[1];
			$rules[$key]['require'] = $check[2];
			$rules[$key]['notify'] = $check[3];
			$rules[$key]['options'] = isset($check[4]) ? $check[4] : NULL;
			$rules[$key]['default'] = isset($check[5]) ? $check[5] : NULL;
			$rules[$key]['alias'] = isset($check[6]) ? $check[6] : NULL;
		}
		
		return isset($rules) ? $rules : array();
	}

	/**
	 * 保存检查通过的值
	 * @param array $rule
	 */
	protected static function setSuccess($key, $rule) {
		// 是否存在别名
		$key = $rule['alias'] ? $rule['alias'] : $key;
		
		// 是否填充默认值
		if($rule['value'] === NULL && $rule['default']) {
			$rule['value'] = $rule['default'];
		}
		
		if($rule['value'] !== NULL) {
			static::$success[$key] = trim($rule['value']);
		}
	}

	/**
	 * 保存检查不通过的值
	 * @param unknown $key
	 * @param unknown $rule
	 */
	protected static function setError($key, $rule) {
		static::$error[$key] = $rule['notify'];
	}

	public static function getSuccess() {
		return static::$success;
	}

	public static function getError() {
		return static::$error;
	}
}

class Rule {

	/**
	 * 是否必须传递
	 * @param array $rule 规则数组
	 * @return boolean
	 */
	public static function isNotExists($rule) {
		return $rule['require'] && is_null($rule['value']);
	}

	/**
	 * 是否是整数
	 * @param array $rule 规则数组
	 * @return boolean
	 */
	public static function isNumber($rule) {
		// 是否是数字
		$flag = is_numeric($rule['value']);
		// 最小值检查
		if($flag && isset($rule['options']['min'])) {
			$flag = $rule['value'] >= $rule['options']['min'];
		}
		// 最大值检查
		if($flag && isset($rule['options']['max'])) {
			$flag = $rule['value'] <= $rule['options']['max'];
		}
		
		return $flag;
	}

	/**
	 * 是否是某一个区间值
	 * @param array $rule 规则数组
	 * @return boolean
	 */
	public static function isIn($rule) {
		return in_array($rule['value'], $rule['options']);
	}

	/**
	 * 是否是邮箱
	 * @param array $rule 规则数组
	 * @return boolean
	 */
	public static function isEmail($rule) {
		return filter_var($rule['value'], FILTER_VALIDATE_EMAIL);
	}

	/**
	 * 是否是网址
	 * @param array $rule 规则数组
	 * @return boolean
	 */
	public static function isUrl($rule) {
		return filter_var($rule['value'], FILTER_VALIDATE_URL);
	}

	/**
	 * 是否是ip地址
	 * @param array $rule 规则数组
	 * @return boolean
	 */
	public static function isIp($rule) {
		return filter_var($rule['value'], FILTER_VALIDATE_IP);
	}

	/**
	 * 使用正则表达式进行检查
	 * @param array $rule 规则数组
	 * @return boolean
	 */
	public static function isRegexp($rule) {
		return preg_match($rule['options'], $rule['value']);
	}

	/**
	 * 是否是一个干净的字符串
	 * @param array $rule 规则数组
	 * @param boolean $flag 默认检查通过
	 * @return boolean
	 */
	public static function isString($rule, $flag = TRUE) {
		// xss注入攻击检查
		if(empty($rule['options']['skipXss'])) {
			$flag = !preg_match('/(<script|<iframe|<link|<frameset|<vbscript|<form|<\?php|document.cookie|javascript:)/i', $rule['value']);
		}
		
		// 字符串长度
		$length = mb_strlen($rule['value']);
		// 最小值检查
		if($flag && isset($rule['options']['min'])) {
			$flag = $length >= $rule['options']['min'];
		}
		// 最大值检查
		if($flag && isset($rule['options']['max'])) {
			$flag = $length <= $rule['options']['max'];
		}
		
		return $flag;
	}

	/**
	 * 是否是手机号码
	 * @param array $rule 规则数组
	 * @param boolean $flag 默认检查通过
	 * @return boolean
	 */
	public static function isMobile($rule) {
		return preg_match("/^1(3|4|5|7|8)[0-9]{9}$/", $rule['value']);
	}

	/**
	 * 是否是电话号码
	 * @param array $rule 规则数组
	 * @param boolean $flag 默认检查通过
	 * @return boolean
	 */
	public static function isPhone($rule) {
		return preg_match("/(\d{3}-)(\d{8})$|(\d{4}-)(\d{7,8})$/", $rule['value']);
	}

	/**
	 * 是否是一个qq号码
	 * @param array $rule 规则数组
	 * @param boolean $flag 默认检查通过
	 * @return boolean
	 */
	public static function isQQ($rules) {
		return preg_match('/^[1-9][0-9]{4,9}$/', $rules['value']);
	}

	/**
	 * 使用自定义方法进行检查
	 * @param array $rule 规则数组
	 * @param boolean $flag 默认检查通过
	 * @return boolean
	 */
	public static function isCallback($rule) {
		return call_user_func($rule['options'], $rule['value']);
	}
}