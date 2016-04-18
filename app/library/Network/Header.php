<?php

/**
 * HTTP头信息汇总类,暂存，怕忘记了
 * @author enychen
 */
namespace Network;

class Header {

	/**
	 * 允许跨域设置cookie
	 * @return void
	 */
	public static function p3p() {
		header('P3P: CP="CURa ADMa DEVa PSAo PSDo OUR BUS UNI PUR INT DEM STA PRE COM NAV OTC NOI DSP COR"');
	}

	/**
	 * 跨域的访问控制
	 */
	public static function AccessControl() {
		header("Access-Control-Allow-Origin: enychen.com");
		header('Access-Control-Allow-Headers: X-Requested-With,X_Requested_With');
	}
}