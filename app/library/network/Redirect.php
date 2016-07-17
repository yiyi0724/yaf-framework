<?php

/**
 * 页面跳转
 * @author enychen
 * @version 1.0
 */
namespace network;

class Redirect {

	/**
	 * 跳转头信息
	 * @var array
	 */
	protected static $headers = array(
		301=>"HTTP/1.1 301 Moved Permanently", 
		302=>"HTTP/1.1 302 Found", 
		303=>"HTTP/1.1 303 See Other", 
		307=>"HTTP/1.1 307 Temporary Redirect"
	);

	/**
	 * 头信息跳转
	 * @static
	 * @param string $url 跳转地址
	 * @param int $code 跳转状态码，可选
	 * @return void
	 */
	public static function getWithoutReferer($url, $code = NULL) {
		// 附加状态码
		if(isset(static::$headers[$code])) {
			header(static::$header[$code]);
		}
		// 进行跳转
		header("Location: {$url}");
		exit();
	}

	/**
	 * 带HTTP_REFERER的get方式页面跳转(使用<meta>进行跳转)
	 * @static
	 * @param string $url 跳转地址
	 * @return void
	 */
	public static function getWithReferer($url) {
		exit("<meta http-equiv=\"refresh\" content=\"0;url={$url}\">");
	}

	/**
	 * 带HTTP_REFERER的post方式页面跳转(使用<form>进行跳转)
	 * @static
	 * @param string $url 跳转地址
	 * @param array $data 附加参数
	 * @return void
	 */
	public static function post($url, array $data = array()) {
		$html = "<form id=\"formSubmit\" style=\"display:none\" method=\"post\" action=\"{$url}\">";
		foreach($data as $key=>$value) {
			$html .= "<input type=\"hidden\" name=\"{$key}\" value=\"{$value}\">";
		}
		$html .= "</form><script type='text/javascript'>document.forms['formSubmit'].submit();</script>";
		exit($html);
	}
}