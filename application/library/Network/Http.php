<?php

/**
 * Http请求类
 * @author enychen
 * @version 1.0
 *
 * @example
 * $http = new Http($url, $decode, $return, $header);
 * 1. $url		要请求的url地址
 * 2. $decode	是否对结果进行解析, json: \Network\Http::DECODE_JSON, xml: \Network\Http::DECODE_XML
 * 3. $return	结果是否返回, 默认返回
 * 4. $header	启用时会将头信息作为数据流输出, 默认禁用
 * 
 * 
 * 可选的方法:
 * 1. 设置要发送的cookie信息: 	$http->setCookie($cookie); $cookie的形式: key=value; key=value 或者 array('key'=>'value')
 * 2. 设置要发送的header信息: 	$http->setHeader($headers); $headers 是一个字符串 或者 array('xxx', 'xxx')的格式
 * 3. 如果要上传文件,请使用: 	$data['upload'] = $http->getFile(string 文件名); 由于php版本的问题,我封装了这个解决方法
 * 4. 设置CURLOPT选项: 			$http->setCurlOpt(CURLOPT_*, $value);
 * 
 * $mthod可以使用的方法: get | post | put | delete | upload
 * list($result, $error) = $http->$method(array 要传递的参数);
 * if($error) {
 * 		// 执行失败
 * } else { 		
 * 		// 执行成功
 * }
 */
namespace Network;

class Http
{
	/**
	 * json解析
	 * @var const
	 */
	const DECODE_JSON = 1;
	
	/**
	 * xml解析
	 * @var const
	 */
	const DECODE_XML = 2;

	/**
	 * 请求地址
	 * @var string
	 */
	protected $action = NULL;

	/**
	 * 连接资源
	 * @var resource
	 */
	protected $curl = NULL;

	/**
	 * 错误信息
	 * @var string
	 */
	protected $error = NULL;

	/**
	 * 请求结果
	 * @var string
	 */
	protected $result = NULL;

	/**
	 * 结果解析
	 * @var bool
	 */
	protected $decode = NULL;

	/**
	 * 构造函数
	 * @param string $action 要请求的url地址
	 * @param string $decode 是否要解析，只支持xml或者json解析方式
	 * @param bool $return 结果是否返回，如果不返回则直接输出，默认返回不输出
	 * @param bool $header　启用时会将头文件的信息作为数据流输出, 默认不输出
	 */
	public function __construct($action, $decode = NULL, $return = TRUE, $header = FALSE)
	{
		// 创建连接资源
		$this->curl = curl_init();
		// url地址
		$this->action = $action;
		// 设置选项,不输出头信息和结果返回
		curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, $return);
		curl_setopt($this->curl, CURLOPT_HEADER, $header);
		// 结果是否要解析,如果需要返回或者需要输出头信息,则不进行解析,因为解析一定失败
		$this->decode = (!($return && $header)) && $decode ? strtolower($decode) : NULL;
	}

	/**
	 * 执行请求
	 * @param string $method 请求方法,get|post|put|delete|upload
	 * @param array $fields 要传递的参数
	 * @return bool 请求成功或失败
	 */
	public function __call($method, $fields)
	{
		$fields = $this->setFields($fields, $method != 'upload');
		switch($method)
		{
			case 'get':
				$fields and ($this->action = "{$this->action}?{$fields}");
				break;
			case 'post':
			case 'put':
			case 'delete':
				curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, strtoupper($method));
				curl_setopt($this->curl, CURLOPT_HTTPHEADER, array('Content-Length: ' . mb_strlen($fields)));
				curl_setopt($this->curl, CURLOPT_POSTFIELDS, $fields);
				break;
			case 'upload':
				curl_setopt($this->curl, CURLOPT_POST, TRUE);
				curl_setopt($this->curl, CURLOPT_POSTFIELDS, $fields);
				break;
			default:
				trigger_error("Fatal Error: NOT FOUND METHOD Http::{$mthod}()");
		}
		
		try
		{
			// 设置请求的地址
			curl_setopt($this->curl, CURLOPT_URL, $this->action);
			// 执行请求
			$this->result = curl_exec($this->curl);
			// 进行解析
			in_array($this->decode, array(static::DECODE_JSON, static::DECODE_XML)) and $this->decode($this->result);
		}
		catch(\Exception $e)
		{
			$this->result = null;
			$this->error = $e->getMessage();
		}
		
		// 关闭连接
		curl_close($this->curl);
		
		// 返回结果和错误
		return array($this->result, $this->error);
	}

	/**
	 * 处理参数
	 * @param array $fields 参数
	 * @param bool $httpBuild 是否数组转换
	 * @return array|string
	 */
	protected function setFields($fields, $httpBuild = TRUE)
	{
		isset($fields[0]) and ($fields = $fields[0]);
		return $httpBuild ? http_build_query($fields) : $fields;
	}

	/**
	 * 解析结果成数组
	 * @param string $result curl的返回结果
	 * @throws \Exception
	 */
	protected function decode($result)
	{
		// xml解析
		if($this->decode == static::DECODE_XML)
		{
			$this->result = @simplexml_load_string($this->result, 'SimpleXMLElement', LIBXML_NOCDATA);
			if(!$this->result)
			{
				throw new \Exception("XML DECODE ERROR, ORIGIN DATA:{$result}");
			}
			$this->result = json_encode($this->result);
		}
		
		// 通用解析,其实是json解析
		// 因为如果是xml,则会先把xml进行json_encode,所以此处可以直接json_decode
		$this->result = json_decode($this->result, TRUE);
		if(json_last_error())
		{
			throw new \Exception("DECODE ERROR, ORIGIN DATA:{$result}");
		}
	}

	/**
	 * 设置头信息
	 * @param array|string $header 头信息数组
	 */
	public function setHeader($headers)
	{
		curl_setopt($this->curl, CURLOPT_HTTPHEADER, is_array($headers) ? $headers : array($headers));
	}

	/**
	 * 设置cookie
	 * @param string|array $cookie cookie信息
	 */
	public function setCookie($origin)
	{
		$cookie = $origin;
		if(is_array($origin))
		{
			$cookie = array();
			foreach($origin as $key=>$val)
			{
				$cookie[] = "{$key}={$val}";
			}
			$cookie = implode('; ', $cookie);
		}
		curl_setopt($this->curl, CURLOPT_COOKIE, $cookie);
	}

	/**
	 * 设置curlopt的设置
	 * @param string $key CURLOPT_*设置选项,参照http://php.net/manual/zh/function.curl-setopt.php
	 * @param mixed $value 值
	 */
	public function setCurlopt($key, $value)
	{
		curl_setopt($this->curl, $key, $value);
	}

	/**
	 * 上传文件的创建方式
	 * @param string $path 文件的绝对路径
	 * @return \CURLFile|string 上传文件对象或者字符串
	 */
	public function getFile($path)
	{
		return class_exists('\CURLFile') ? new \CURLFile($path) : "@{$path}";
	}
}