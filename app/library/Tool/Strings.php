<?php

/**
 * 字符串操作封装
 * @author enychen
 */
namespace Tool;

class Strings {

	/**
	 * 进行html编码
	 * @param string $string 需要编码的字符串
	 * @return string
	 */
	public static function htmlEncode($string) {
		return htmlspecialchars($string, \ENT_QUOTES | \ENT_COMPAT | \ENT_HTML401);
	}
	
	/**
	 * 进行html解码
	 * @param string $string 需要解码的字符串
	 * @return string
	 */
	public static function htmlDecode($string) {
		return htmlspecialchars_decode($string, \ENT_QUOTES | \ENT_COMPAT | \ENT_HTML401);
	}
	
	/**
	 * 模糊化电话号码
	 * @param string $phone 手机号码或者固定电话
	 * @return string
	 */
	public static function luzzyMobile($mobile) {
		$telPattern = '/(0[0-9]{2,3}[-]?[2-9])[0-9]{3,4}([0-9]{3}[-]?[0-9]?)/i';
		$mobilePattern = '/(1[34578]{1}[0-9])[0-9]{4}([0-9]{4})/i';
		$pattern = preg_match('/(0[0-9]{2,3}[-]?[2-9][0-9]{6,7}[-]?[0-9]?)/i', $mobile) ? $telPattern : $mobilePattern;
		return preg_replace($pattern, '$1****$2', $mobile);
	}
	
	/**
	 * 模糊化邮箱（目前只支持英文邮箱）
	 * @param string $mail 邮箱地址
	 * @return string|null 如果不是邮箱地址，返回NULL，否则返回模糊后的账号
	 */
	public static function luzzyEmail($mail) {
		$mail = explode('@', $mail);
		if(count($mail) != 2) {
			return NULL;
		}
		$length = mb_strlen($mail[0]);
		$mail[0] = str_pad(mb_substr($mail[0], 0, 2), $length, '*');
		return implode('@', $mail);
	}

	/**
	 * 格式化数字
	 * @param number $number 要格式化的数字
	 * @param number $decimals 要保留的小数位数
	 * @param string $decPoint 指定小数点显示的字符
	 * @param string $thousandsSep 指定千位分隔符显示的字符
	 * @return string
	 */
	public static function numberFormat($number, $decimals = 0, $decPoint = '.', $thousandsSep = ',') {
		return number_format($number, $decimals, $decPoint, $thousandsSep);
	}
}