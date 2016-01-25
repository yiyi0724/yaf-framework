<?php

/**
 * Http请求类
 * @author enychen
 * @version 1.0
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
	 * @param int $decode 是否要解析，只支持xml或者json解析方式, json: \Network\Http::DECODE_JSON, xml: \Network\Http::DECODE_XML
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
		$this->decode = (!($return && $header)) && $decode ? $decode : NULL;
	}

	/**
	 * 执行请求
	 * @param string $method 请求方法,get|post|put|delete|upload
	 * @param array $fields 要传递的参数
	 * @return bool 请求成功或失败
	 */
	public function __call($method, $fields)
	{
		// 读取数据
		$fields = isset($fields[0]) ? $fields[0] : array();
		// 进行操作
		switch($method)
		{
			case 'get':
				$fields and ($this->action = "{$this->action}?" . http_build_query($fields));
				break;
			case 'post':
			case 'put':
			case 'delete':
				curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, strtoupper($method));
				curl_setopt($this->curl, CURLOPT_HTTPHEADER, array(
					'Content-Length: ' . mb_strlen($fields)
				));
				curl_setopt($this->curl, CURLOPT_POSTFIELDS, http_build_query($fields));
				break;
			case 'upload':
				curl_setopt($this->curl, CURLOPT_POST, TRUE);
				curl_setopt($this->curl, CURLOPT_POSTFIELDS, $fields);
				break;
			default:
				throw new \Exception("Fatal Error: NOT FOUND METHOD Http::{$mthod}()");
		}
		// 设置请求的地址
		curl_setopt($this->curl, CURLOPT_URL, $this->action);
		// 执行请求
		$this->result = curl_exec($this->curl);
		// 进行解析
		in_array($this->decode, array(
			static::DECODE_JSON, static::DECODE_XML
		)) and $this->decode($this->result);
		
		// 关闭连接
		curl_close($this->curl);
		
		// 返回结果和错误
		return $this->result;
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
		curl_setopt($this->curl, CURLOPT_HTTPHEADER, is_array($headers) ? $headers : array(
			$headers
		));
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