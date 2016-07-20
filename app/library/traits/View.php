<?php

/**
 * 模板对象
 * @author enychen
 */
namespace traits;

use \Yaf\View\Simple;

class view extends Simple {

	/**
	 * 加载公共layout模板
	 * @param string $tpl 模板名称
	 * @param array $tpl_vars 视图数据
	 * @return void
	 */
	public function commonLayout($tpl, array $tpl_vars = array()) {
		$this->setScriptPath(sprintf('%slayout', COMMON_VIEW_PATH));
		echo parent::render("{$tpl}.phtml", $tpl_vars);
	}

	/**
	 * 加载模块layout文件
	 * @param string $tpl 模板名称
	 * @param array $tpl_vars 视图数据
	 * @return void
	 */
	public function moduleLayout($tpl, array $tpl_vars = array()) {
		$this->setScriptPath(sprintf('%slayout', MODULE_VIEW_PATH));
		echo parent::render("{$tpl}.phtml", $tpl_vars);
	}

	/**
	 * 格式化数字
	 * @param number $number 要格式化的数字
	 * @param number $decimals 要保留的小数位数
	 * @param string $decPoint 指定小数点显示的字符
	 * @param string $thousandsSep 指定千位分隔符显示的字符
	 * @return string
	 */
	public function numberFormat($number, $decimals = 0, $decPoint = '.', $thousandsSep = ',') {
		return number_format($number, $decimals, $decPoint, $thousandsSep);
	}

	/**
	 * 将时间戳转成某一个格式的时间
	 * @param number $timestamp 时间戳, 如果不输出则输出格式化当前时间
	 * @param string $format 格式化样式，默认是Y-m-d H:i:s
	 * @return string
	 */
	public function formatDate($timestamp = NULL, $format = 'Y-m-d H:i:s') {
		return date($format, (is_null($timestamp) ? time() : $timestamp));
	}

	/**
	 * 简化isset($data) ? $data : NULL的作用
	 * @param array $data 数组数据
	 * @param array $key 要获取的key
	 * @param mixed $default 如果不存在则输出
	 * @return mixed
	 */
	public function simplifyIsset($data, $key, $default = NULL) {
		return isset($data[$key]) ? $data[$key] : $default;
	}

	/**
	 * 进行html编码
	 * @param string $string 需要编码的字符串
	 * @return string
	 */
	public function htmlEncode($string) {
		return htmlspecialchars($string, \ENT_QUOTES | \ENT_COMPAT | \ENT_HTML401);
	}

	/**
	 * 进行html解码
	 * @param string $string 需要解码的字符串
	 * @return string
	 */
	public function htmlDecode($string) {
		return htmlspecialchars_decode($string, \ENT_QUOTES | \ENT_COMPAT | \ENT_HTML401);
	}

	/**
	 * 模糊化电话号码
	 * @param string $phone 手机号码或者固定电话
	 * @return string
	 */
	public function luzzyPhone($phone) {
		$telPattern = '/(0[0-9]{2,3}[-]?[2-9])[0-9]{3,4}([0-9]{3}[-]?[0-9]?)/i';
		$mobilePattern = '/(1[34578]{1}[0-9])[0-9]{4}([0-9]{4})/i';
		$pattern = preg_match('/(0[0-9]{2,3}[-]?[2-9][0-9]{6,7}[-]?[0-9]?)/i', $phone) ? $telPattern : $mobilePattern;
		return preg_replace($pattern, '$1****$2', $phone);
	}

	/**
	 * 模糊化邮箱（目前只支持英文邮箱）
	 * @param string $mail 邮箱地址
	 * @return string|null 如果不是邮箱地址，返回NULL，否则返回模糊后的账号
	 */
	public function luzzyEmail($mail) {
		$mail = explode('@', $mail);
		if(count($mail) != 2) {
			return NULL;
		}
		$length = mb_strlen($mail[0]);
		$mail[0] = str_pad(mb_substr($mail[0], 0, 2), $length, '*');
		return implode('@', $mail);
	}
}