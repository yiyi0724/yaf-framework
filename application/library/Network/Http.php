<?php
/**
 * Http请求类
 * @author chenxb
 * @version 1.1
 *
 * @example
 * $http = new Http();
 * $http->setAction($url);
 * 
 * 可选的方法:
 * 1. 结果自动json解析: $http->setJsonDecode();
 * 2. 设置要发送的cookie信息: $http->setCookie(string $cookie); cookie信息必须是key=value; key=value的形式
 * 3. 设置要发送的header信息: $http->setHeader(array $headers); header就是一个个头信息的数组
 * 4. 如果要上传文件,请使用: $data['upload'] = $http->getFile(string 文件名); 由于php版本的问题,我封装了这个解决方法
 * 5. 设置CURLOPT选项: $http->setCurlOpt(CURLOPT_*, $value);
 * 
 * $mthod可以使用的方法: get | post | put | delete | upload
 * if($http->$method(array 要传递的参数)) {
 * 		// 执行成功
 * 		$result = $this->getResult();
 * } else {
 * 		// 执行失败
 * 		$error = $this->getError();
 * }
 */

namespace Network;

class Http
{
	/**
	 * 请求地址
	 * @var string
	 */
	protected $action = null;
	
	/**
	 * 连接资源
	 * @var resource
	 */
	protected $curl = null;
	
	/**
	 * 错误信息
	 * @var string
	 */
	protected $error = null;
	
	/**
	 * 请求结果
	 * @var string
	 */
	protected $result = null;
		
	/**
	 * json解析
	 * @var bool
	 */
	protected $jsonDecode = false;
	
	/**
	 * 构造函数
	 */
	public function __construct()
	{
		// 创建连接资源
		$this->curl = curl_init();
		
		// 设置选项,不输出头信息和结果返回
		curl_setopt($this->curl, CURLOPT_HEADER, 0);
		curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, 1);
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
				$fields AND ($this->action = "{$this->action}?{$fields}");
				break;
			case 'post':				
			case 'put':
			case 'delete':
				curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, strtoupper($method));
				curl_setopt($this->curl, CURLOPT_HTTPHEADER, array('Content-Length: '.mb_strlen($fields))); 
				curl_setopt($this->curl, CURLOPT_POSTFIELDS, $fields);
				break;
			case 'upload':
				curl_setopt($this->curl, CURLOPT_POST, true);
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
			// 返回的结果状态判断状态
			$this->checkStatus();
			// 进行json解析
			$this->decodeJson AND $this->dealResult($this->result);
		}
		catch(\Exception $e)
		{
			$this->result = null;
			$this->error = $e->getMessage();
		}
		
		// 返回错误
		return !$this->error;
	}
	
	/**
	 * 处理参数
	 * @param array $fields 参数
	 * @param bool $httpBuild 是否数组转换
	 * @return array|string
	 */
	protected function setFields($fields, $httpBuild=true)
	{
		isset($fields[0]) AND ($fields = $fields[0]);
		return $httpBuild ? http_build_query($fields) : $fields;
	}
	
	/**
	 * 结果处理
	 * @param string $result curl的返回结果
	 * @throws \Exception
	 */
	protected function dealResult($result)
	{
		$this->result = json_decode($this->result, true);
		if(json_last_error())
		{
			throw new \Exception("JSON DECODE ERROR, ORIGIN DATA:{$result}");
		}
	}
	
	/**
	 * 响应状态码检查
	 * @throws \Exception
	 */
	protected function checkStatus()
	{
		if(!in_array(curl_getinfo($this->curl, CURLINFO_HTTP_CODE), array(200, 201, 202, 203, 204, 205)))
		{
			throw new \Exception('CURL TIMEOUT');
		}
	}
	
	/**
	 * 设置url地址
	 * @param string $action url地址
	 */
	public function setAction($action)
	{
		$this->action = $action;
	}
	
	/**
	 * 设置头信息
	 * @param array $header 头信息数组
	 */
	public function setHeader(array $headers)
	{
		curl_setopt($this->curl, CURLOPT_HTTPHEADER, $headers);
	}
	
	/**
	 * 设置cookie
	 * @param string $cookie cookie信息
	 */
	public function setCookie($cookie)
	{
		curl_setopt($this->curl, CURLOPT_COOKIE, $cookie);
	}
	
	/**
	 * 设置curlopt的设置
	 * @param string $key CURLOPT_*设置选项
	 * @param mixed $value 值
	 */
	public function setCurlopt($key, $value)
	{
		curl_setopt($this->curl, $key, $value);
	}
	
	/**
	 * 开启json解析
	 */
	public function setJsonDecode()
	{
		$this->jsonDecode = true;
	}

	/**
	 * 获取错误
	 * @return string
	 */
	public function getError()
	{
		return $this->error;
	}
		
	/**
	 * 获取结果
	 * @return mixed
	 */
	public function getResult()
	{
		return $this->result;
	}

	/**
	 * 上传文件的创建方式
	 * @param string $path 文件的绝对路径
	 * @return mixed
	 */
	public function getFile($path)
	{
		return class_exists('\CURLFile') ?  new \CURLFile($path) : "@{$path}";
	}
	
	/**
	 * 关闭连接
	 */
	public function close()
	{
		curl_close($this->curl);
	}
}