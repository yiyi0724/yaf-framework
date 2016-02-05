<?php

/**
 * 页面跳转
 * @author enychen
 * @version 1.0
 */
namespace Network;

class Location
{	
	/**
	 * 跳转头信息
	 * @var array
	 */
	protected static $headers = array(
		301 => "HTTP/1.1 301 Moved Permanently",
		302 => "HTTP/1.1 302 Found",
		303 => "HTTP/1.1 303 See Other",
		307 => "HTTP/1.1 307 Temporary Redirect",
	);
	
	/**
	 * 不带HTTP_REFERER的跳转
	 * @param string $url 跳转地址
	 * @param int $code 跳转状态码
	 */
	public static function redirect($url, $code = NULL)
	{		
		$code and isset(static::$headers[$code]) and header(static::$header[$code]);
		header("Location: {$url}");
	}
	
	/**
	 * get方式页面跳转
	 * @param string $url 跳转地址
	 */
	public static function get($url)
	{
		echo "<meta http-equiv=\"refresh\" content=\"0;url={$url}\">";
	}
	
	/**
	 * post方式页面跳转
	 * @param string $url 跳转地址
	 * @param array $data 附加参数
	 */
	public static function post($url, array $data=array())
	{
		$html = "<form id=\"formSubmit\" style=\"display:none\" method=\"post\" action=\"{$url}\">";
		foreach($data as $key=>$value)
		{
			$html .= "<input type=\"hidden\" name=\"{$key}\" value=\"{$value}\">";
		}
		$html .= "</form><script>document.forms['formSubmit'].submit();</script>";		
		echo $html;
	}
}