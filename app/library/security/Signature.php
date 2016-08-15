<?php

/**
 * 生成签名串
 * @author enychen
 */
namespace security;

class Signature {

	/**
	 * 生成md5签名串
	 * @static
	 * @param array $data 待生成签名的字符串
	 * @param string $key 密钥
	 * @return string
	 */
	public static function create(array $data, $key) {
		// 删除不必要的字符串
		foreach($data as $key=>$value) {
			if($key == 'sign' || !$value || is_array($value)) {
				unset($data[$key]);
			}
		}
		// 进行排序
		ksort($data);
		
		// 返回签名串
		return md5(urldecode(http_build_query($data)) . $key);
	}

	/**
	 * 对比md5签名
	 * @static
	 * @param array $data 待生成签名的字符串
	 * @param string $key 密钥
	 * @return boolean
	 */
	public static function compare(array $data, $key) {
		return (!empty($data['sign'])) || (self::signByMd5($data, $key) == $data['sign']);
	}
}