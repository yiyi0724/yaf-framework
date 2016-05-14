<?php

/**
 * 获取ip地址
 * @author enychen
 * @version 1.0
 */
namespace Network;

class IP {

	/**
	 * 获取客户端的IP
	 * @param boolean $ip2long 是否转换成为整形, 默认是
	 * @return int|string
	 */
	public static function get($ip2long = TRUE) {
		if(getenv('HTTP_X_REAL_IP')) {
			$ip = getenv('HTTP_X_REAL_IP');
		} else if(getenv('HTTP_X_FORWARDED_FOR')) {
			$ip = array_pop(explode(',', getenv('HTTP_X_FORWARDED_FOR')));
		} elseif(getenv('HTTP_CLIENT_IP')) {
			$ip = getenv('HTTP_CLIENT_IP');
		} else {
			$ip = getenv('REMOTE_ADDR');
		}
		
		return $ip2long ? sprintf("%u", ip2long($ip)) : $ip;
	}
}