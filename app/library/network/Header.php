<?php

/**
 * HTTP头信息汇总类
 * @author enychen
 */
namespace network;

class Header {

	/**
	 * 允许跨域设置cookie（p3p头信息设置）
	 * @static
	 * @return void
	 */
	public static function p3p() {
		header('P3P: CP="CURa ADMa DEVa PSAo PSDo OUR BUS UNI PUR INT DEM STA PRE COM NAV OTC NOI DSP COR"');
	}

	/**
	 * 跨域的访问控制可以跨域的域名
	 * @static
	 * @param string $domain 允许进行跨域的域名，多个域名用,隔开，必须设置完整到协议
	 * @return void
	 */
	public static function accessControlAllowOrigin($domain = '*') {
		header("Access-Control-Allow-Origin: {$domain}");
	}

	/**
	 * 如果存在跨域自定义头信息，需要输出头信息控制
	 * @static
	 * @param string $headers 多个头信息，用逗号隔开
	 * @return void
	 */
	public static function accessControlAllowHeaders($headers) {
		header("Access-Control-Allow-Headers: {$headers}");
	}
}