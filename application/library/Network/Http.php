<?php
/**
 * Http请求类
 * @author chenxb
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
	 * 请求是否有误
	 * @var string
	 */
	protected $error = null;
	
	/**
	 * 请求的结果
	 * @var string
	 */
	protected $result = null;
		
	/**
	 * 结果进行json解析
	 * @var bool
	 */
	protected $jsonDecode = true;
	
	/**
	 * 构造函数
	 */
	public function __construct($action)
	{
		// 请求地址
		$this->action = $action;

		
		// 创建连接资源
		$this->curl = curl_init();
		
		// 设置选项,不输出头信息和结果返回
		curl_setopt($this->curl, CURLOPT_HEADER, 0);
		curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, 1);
	}
	
	/**
	 * 执行请求
	 * @param 请求方法 $method get|post|put|delete
	 * @param 附加参数 $args 无用
	 */
	public function __call($method, $fields)
	{		
		switch($method)
		{
			case 'get':
				$fields = $this->setFields($fields);
				$fields AND ($this->action = "{$this->action}?{$fields}");
				break;
			case 'post':				
			case 'put':
			case 'delete':
				$fields = $this->setFields($fields);
				curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, strtoupper($method));
				curl_setopt($this->curl, CURLOPT_HTTPHEADER, array('Content-Length: '.mb_strlen($fields))); 
				curl_setopt($this->curl, CURLOPT_POSTFIELDS, $fields);
				break;
			case 'upload':
				$fields = $this->setFields($fields, false);
				curl_setopt($this->curl, CURLOPT_POST, true);
				curl_setopt($this->curl, CURLOPT_POSTFIELDS, $fields);
				break;
			default:
				throw new \Exception("NOT FOUND METHOD Http::{$mthod}()");
		}
		
		try
		{
			// 设置请求的地址
			curl_setopt($this->curl, CURLOPT_URL, $this->action);
			// 执行请求
			$this->result = curl_exec($this->curl);
			// 返回的结果状态判断状态
			if(!in_array(curl_getinfo($this->curl, CURLINFO_HTTP_CODE), array(200, 201, 202, 203, 204, 205)))
			{
				throw new \Exception('CURL NETWORK TIMEOUT');
			}			
			// 进行json解析
			$this->decodeJson AND $this->dealResult($this->result);
		}
		catch(\Exception $e)
		{
			$this->result = null;
			$this->error = $e->getMessage();
		}
		
		// 关闭连接
		curl_close($this->curl);
		
		// 返回错误
		return !$this->error;
	}
	
	/**
	 * 处理参数
	 * @param array $fields
	 */
	protected function setFields($fields, $httpBuild=true)
	{
		isset($fields[0]) AND ($fields =  $fields[0]);
		return $httpBuild ? http_build_query($fields) : $fields;
	}
	
	/**
	 * 结果处理
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
	 * 设置头信息
	 * @param array $header 头信息数组
	 */
	public function setHeader(array $headers)
	{
		curl_setopt($this->curl, CURLOPT_HTTPHEADER, $headers);
	}
	
	/**
	 * 设置cookie
	 * @param string $cookie
	 */
	public function setCookie($cookie)
	{
		curl_setopt($this->curl, CURLOPT_COOKIE, $cookie);
	}
	
	/**
	 * 关闭对结果的json解析
	 */
	public function setUnJson()
	{
		$this->jsonDecode = false;
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
	 * 获取文件的方式
	 * @param string $path 文件的绝对路径
	 * @return mixed
	 */
	public function getFile($path)
	{
		return class_exists('\CURLFile') ?  new \CURLFile($path) : "@{$path}";
	}
}