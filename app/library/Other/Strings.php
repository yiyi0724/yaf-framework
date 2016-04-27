<?php

namespace Other;

/**
 * 字符串处理的函数
 * @author enychen
 */
class Strings {

	/**
	 * 对HTML字符串进行编码
	 * @param string $value 值
	 * @return string 编码后的字符串
	 */
	public static function htmlEncode($value) {
		return htmlspecialchars($value, ENT_QUOTES | ENT_COMPAT | ENT_HTML401);
	}

	/**
	 * 对HTML字符串字符串进行解码
	 * @param string $value 值
	 * @return string 解码后的字符串
	 */
	static public function htmlDecode($value) {
		return htmlspecialchars_decode($value, ENT_QUOTES | ENT_COMPAT | ENT_HTML401);
	}
	
	/**
	 * 模糊化手机号码
	 * @param string $value 值
	 * @return string 模糊以后的字符串
	 */
	public static function fuzzyMobile($value) {
		return substr($value, 0, 3) . '*****' . substr($value, -3);
	}
	
	/**
	 * 模糊化邮箱地址
	 *
	 * @param string $email
	 * @return string
	 */
	public static function fuzzyEmail($value) {		
		list($prefix, $domain) = explode('@', $value);
		
		return self::fuzzy($name) . '@' . $domain;
	}

	/**
	 * 判断字符串是否仅是 英文, 中文(utf-8), 数字 组合
	 * @param string $str   所要判断的字符串
	 * @return boolean
	 */
	static public function matchAsciiChineseNum($str = '') {
		return preg_match('/^[a-z|A-Z|0-9|\x{4e00}-\x{9fa5}]$/u', $str) ? true : false;
	}

	/**
	 * 支持UTF8的字符串替换
	 * @param string $string
	 * @param string $replacement
	 * @param int $start
	 * @param int $length
	 * @param string $encoding
	 * @return string
	 */
	public static function substrReplace($string, $replacement, $start, $length = null, $encoding = 'UTF-8') {
		$string_length = mb_strlen($string, $encoding);
		
		if($start < 0) {
			$start = max(0, $string_length + $start);
		} else if($start > $string_length) {
			$start = $string_length;
		}
		
		if($length < 0) {
			$length = max(0, $string_length - $start + $length);
		} else if((is_null($length) === true) || ($length > $string_length)) {
			$length = $string_length;
		}
		
		if(($start + $length) > $string_length) {
			$length = $string_length - $start;
		}
		
		return mb_substr($string, 0, $start, $encoding) . $replacement . mb_substr($string, abs($start + $length), ceil($string_length - $start - $length), $encoding);
	}
}
