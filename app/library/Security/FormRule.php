<?php

namespace \Security;

/**
 * 规则数组
 * @author eny
 *
 */
class FormRule {

	/**
	 * 是否是整数
	 * @param int $value 参数
	 * @param array $options 可选检查项目：min|最小值， max|最大值
	 * @return boolean
	 */
	public static function isNumber($value, array $options = array()) {
		$flag = is_numeric($value);
		$flag = ($flag && isset($options['min'])) ?  $value >= $options['min'] : $flag;
		$flag = ($flag && isset($options['min'])) ?  $value <= $options['max'] : $flag;
		return $flag;
	}

	/**
	 * 是否是某一个区间值
	 * @param int|string $value 参数
	 * @param array $range 区间数组
	 * @return boolean
	 */
	public static function isIn($value, array $range) {
		return in_array($value, $range);
	}

	/**
	 * 是否是邮箱
	 * @param string $value 参数
	 * @return boolean
	 */
	public static function isEmail($value) {
		return (bool)filter_var($value, FILTER_VALIDATE_EMAIL);
	}

	/**
	 * 是否是网址,必须包含协议，如http://
	 * @param sring $value 参数
	 * @return boolean
	 */
	public static function isUrl($value) {
		return (bool)filter_var($value, FILTER_VALIDATE_URL);
	}

	/**
	 * 是否是ip地址
	 * @param int|string $value 参数
	 * @return boolean
	 */
	public static function isIp($value) {
		$value = is_numeric($value) ? long2ip($value) : $value;
		return (bool)filter_var($value, FILTER_VALIDATE_IP);
	}

	/**
	 * 使用正则表达式进行检查
	 * @param array $rule 规则数组
	 * @return boolean
	 */
	public static function isRegexp($value, $pattern) {
		return (bool)preg_match($pattern, $value);
	}

	/**
	 * 是否是一个干净的字符串
	 * @param array $rule 规则数组
	 * @param boolean $flag 默认检查通过
	 * @return boolean
	 */
	public static function isString($value, array $options = array()) {
		$flag = is_string($value) || is_numeric($value);
		if(empty($options['skipXss']) || !$options['skipXss']) {
			$pattern = '/(<script|<iframe|<link|<frameset|<vbscript|<form|<\?php|document.cookie|javascript:)/i';
			$flag = !preg_match($pattern, $value);
		}
		$flag = ($flag && isset($options['min'])) ? mb_strlen($value) >= $options['min'] : $flag;
		$flag = ($flag && isset($options['max'])) ? mb_strlen($value) <= $options['max'] : $flag;

		return $flag;
	}

	/**
	 * 是否是手机号码
	 * @param array $rule 规则数组
	 * @param boolean $flag 默认检查通过
	 * @return boolean
	 */
	public static function isMobile($value) {
		return (bool)preg_match("/^1(3|4|5|7|8)[0-9]{9}$/", $value);
	}

	/**
	 * 是否是电话号码
	 * @param array $rule 规则数组
	 * @param boolean $flag 默认检查通过
	 * @return boolean
	 */
	public static function isPhone($value) {
		return (bool)preg_match("/(\d{3}-)(\d{8})$|(\d{4}-)(\d{7,8})$/", $value);
	}

	/**
	 * 是否是一个qq号码
	 * @param array $rule 规则数组
	 * @param boolean $flag 默认检查通过
	 * @return boolean
	 */
	public static function isQQ($value) {
		return (bool)preg_match('/^[1-9][0-9]{4,9}$/', $value);
	}

	/**
	 * 使用自定义方法进行检查
	 * @param array $rule 规则数组
	 * @param boolean $flag 默认检查通过
	 * @return boolean
	 */
	public static function isCallback($value, $callback) {
		return (bool)call_user_func($callback, $value);
	}
}