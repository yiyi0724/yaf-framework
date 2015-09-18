<?php
/**
 * HTTP头信息
 * @author enychen
 */
namespace Network;

class Header
{
	/**
	 * 允许跨域设置cookie
	 * @return void
	 */
	public static function p3p()
	{
		header('P3P: CP="CURa ADMa DEVa PSAo PSDo OUR BUS UNI PUR INT DEM STA PRE COM NAV OTC NOI DSP COR"');
	}
	
	/**
	 * 缓存控制
	 * @return void
	 */
	public static function cacheControl()
	{
		header('Cache-Control:private, max-age=0, no-cache, must-revalidate, no-cache=Set-Cookie, proxy-revalidate');
	}
}