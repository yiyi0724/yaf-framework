<?php

/**
 * 表单规则类
 * @author enychen
 */
namespace security;

class Is {

	/**
	 * 是否是整数或者整数字符串
	 * @param int $value 待检查的值
	 * @param array $options 可选检查项目：min|最小值， max|最大值
	 * @return boolean 检查通过返回TRUE
	 */
	public static function int($value, array $options = array()) {
		$flag = is_numeric($value);
		$flag = ($flag && isset($options['min'])) ? $value >= $options['min'] : $flag;
		$flag = ($flag && isset($options['max'])) ? $value <= $options['max'] : $flag;
		return $flag;
	}

	/**
	 * 是否在几个值内
	 * @param int|string $value 待检查的值
	 * @param array $range 数组
	 * @return boolean 检查通过返回TRUE
	 */
	public static function in($value, array $range) {
		return in_array($value, $range);
	}

	/**
	 * 是否是邮箱地址
	 * @param string $value 待检查的值
	 * @return boolean 检查通过返回TRUE
	 */
	public static function email($value) {
		return (bool)filter_var($value, FILTER_VALIDATE_EMAIL);
	}

	/**
	 * 是否是合法的请求地址,必须包含协议，如http://，ftp://
	 * @param sring $value 待检查的值
	 * @return boolean 检查通过返回TRUE
	 */
	public static function url($value) {
		return (bool)filter_var($value, FILTER_VALIDATE_URL);
	}

	/**
	 * 是否是ip地址
	 * @param int|string $value 待检查的值
	 * @return boolean 检查通过返回TRUE
	 */
	public static function ip($value) {
		$value = is_numeric($value) ? long2ip($value) : $value;
		return (bool)filter_var($value, FILTER_VALIDATE_IP);
	}

	/**
	 * 使用正则表达式进行检查
	 * @param string $value 待检查的值
	 * @param string $pattern 正则表达式
	 * @return boolean 检查通过返回TRUE
	 */
	public static function regexp($value, $pattern) {
		return (bool)preg_match($pattern, $value);
	}

	/**
	 * 是否是一个干净的字符串
	 * @param string $value 待检查的值
	 * @param array $options 可选检查项目：min|最小长度， max|最大长度，xss|进行xss检查,默认是TRUE
	 * @return boolean 检查通过返回TRUE
	 */
	public static function string($value, array $options = array()) {
		$flag = is_string($value) || is_numeric($value);
		if(empty($options['xss']) || $options['xss']) {
			$pattern = '/(<script|<iframe|<link|<frameset|<vbscript|<meta|<form|<\?php|document.cookie|javascript:|vbscript)/i';
			$flag = !preg_match($pattern, $value);
		}
		$flag = ($flag && isset($options['min'])) ? mb_strlen($value) >= $options['min'] : $flag;
		$flag = ($flag && isset($options['max'])) ? mb_strlen($value) <= $options['max'] : $flag;
		return $flag;
	}

	/**
	 * 是否是中国大陆手机号码
	 * @param string $value 待检查的值
	 * @return boolean 检查通过返回TRUE
	 */
	public static function mobile($value) {
		return (bool)preg_match('/^1(3|4|5|7|8)[0-9]{9}$/', $value);
	}

	/**
	 * 是否是电话号码
	 * @param string $value 待检查的值
	 * @return boolean 检查通过返回TRUE
	 */
	public static function phone($value) {
		return (bool)preg_match('/(\d{3}-)(\d{8})$|(\d{4}-)(\d{7,8})$/', $value);
	}

	/**
	 * 是否是一个qq号码
	 * @param string $value 待检查的值
	 * @return boolean 检查通过返回TRUE
	 */
	public static function qq($value) {
		return (bool)preg_match('/^[1-9][0-9]{4,9}$/', $value);
	}

	/**
	 * 是否是中国身份证号码(18位)
	 * (1)十七位数字本体码加权求和公式
	 *    S = Sum(Ai * Wi), i = 0, ... , 16 ，先对前17位数字的权求和
	 *    Ai:表示第i位置上的身份证号码数字值(0~9)
	 *    Wi:7 9 10 5 8 4 2 1 6 3 7 9 10 5 8 4 2 （表示第i位置上的加权因子）
	 * (2)计算模
	 *    Y = mod(S, 11)
	 * (3)根据模，查找得到对应的校验码
	 *    Y: 0 1 2 3 4 5 6 7 8 9 10
	 *    校验码: 1 0 X 9 8 7 6 5 4 3 2
	 * @param string $value 待检查的值
	 * @return boolean 检查通过返回TRUE
	 */
	public static function identity($value) {
		// 加权因子
		$wi = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);
		// 校验码
		$vi = array('1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2');
		
		$ni = 0;
		$value = (string)$value;
		$len = strlen($value) - 1;
		
		for($i = 0, $max = $len; $i < $max; $i++) {
			$aiv = (int)($value[$i] ?  : 0);
			$wiv = (int)($wi[$i] ?  : 0);
			$ni += ($aiv * $wiv);
		}

		return (bool)(strcasecmp((string)($vi[$ni % 11]), (string)($value[$len])) === 0);
	}

	/**
	 * 使用自定义方法进行检查
	 * @param string $value 待检查的值
	 * @param string $callback 回调字符串
	 * @param boolean $flag 默认检查通过
	 * @return boolean 检查通过返回TRUE
	 */
	public static function callback($value, $callback) {	
		return (bool)call_user_func($callback, $value);
	}
}