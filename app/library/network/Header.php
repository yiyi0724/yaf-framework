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
	 * @param string $domain 允许进行跨域的域名，多个域名用,隔开
	 * @return void
	 */
	public static function accessControlAllowOrigin($domain = '*') {
		header("Access-Control-Allow-Origin: {$domain}");
	}

	/**
	 * 如果存在跨域自定义头信息，需要输出头信息控制
	 * @param string $headers 多个头信息，用逗号隔开
	 * @return void
	 */
	public static function accessControlAllowHeaders($headers) {
		header("Access-Control-Allow-Headers: {$headers}");
	}

	/**
	 * 输出协议码信息
	 * @static
	 * @param int $code 协议码，目前只支持301,302,303,307,403,404
	 * @param bool $exit 是否终止程序运行， 默认TRUE
	 * @return void
	 */
	public static function httpCode($code, $exit = TRUE) {
		$httpCode = array(
			301=>"HTTP/1.1 301 Moved Permanently",
			302=>"HTTP/1.1 302 Found",
			303=>"HTTP/1.1 303 See Other",
			307=>"HTTP/1.1 307 Temporary Redirect",
			403=>'HTTP/1.1 403 Forbidden',
			404=>'HTTP/1.1 404 Not Found',
		);
		
		header($httpCode[$code]);
		$exit and exit();
	}
	
	/**
	 * 设置cookie
	 * @static
	 * @param string $name cookie名称
	 * @param string $value cookie值
	 * @param int $expire 过期时间,直接写上加上或减少的时间，无需time()+3600这样
	 * @param string $path  允许的目录,默认整站
	 * @param string $domain 允许的域名,默认null表示当前域名
	 * @param bool $secure 是否使用https，默认false
	 * @param bool $httponly 是否cookie只读，默认true
	 * @return void
	 */
	public static function setCookie($name, $value, $expire, $path='/', $domain=NULL, $secure=FALSE, $httponly=TRUE) {
		setcookie($name, $value, (time() + $expire), $path, $domain, $secure, $httponly);
	}
}