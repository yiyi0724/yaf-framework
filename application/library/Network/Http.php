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
	 * 传递的参数
	 * @var string
	 */
	protected $fields = null;
	
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
	 * 
	 * @param 请求方法 $method get|post|put|delete
	 * @param 附加参数 $args 无用
	 */
	public function __call($method, $args)
	{
		switch($method)
		{
			case 'get':
				$this->fields AND 
					($this->action = "{$this->action}?{$this->fields}");
				break;
			case 'post':				
			case 'put':
			case 'delete':
				curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, strtoupper($method));
      			curl_setopt($this->curl, CURLOPT_HTTPHEADER, array('Content-Length: '.mb_strlen($this->fields))); 
      			curl_setopt($this->curl, CURLOPT_POSTFIELDS, $this->fields);
				break;
			case 'upload':
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
			// 返回的结果状态
			$status = curl_getinfo($this->curl, CURLINFO_HTTP_CODE);
			// 判断状态
			if(!in_array($status, array(200, 201, 202, 203, 204, 205)))
			{
				throw new \Exception('CURL NETWORK TIMEOUT');
			}
		}
		catch(\Exception $e)
		{
			$this->result = null;
			$this->error = $e->getMessage();
		}
		
		// 关闭连接
		curl_close($this->curl);
		
		// 返回错误
		return $this->error;
	}
	
	/**
	 * 请求的data数据
	 * @param array $fields 要传递的参数
	 */
	public function setFields(array $fields)
	{
		$this->fields = http_build_query($fields);
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
	 * 结果输出
	 */
	public function enableOutput()
	{
		curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, 0);
	}
		
	/**
	 * 获取结果
	 * @return mixed
	 */
	public function getResult()
	{
		return $this->result;
	}
}

$http = new \Network\Http('http://www.test.com/api.php');
$http->enableOutput();
$http->setFields(array('upload'=>'@/tmp/ip.php', 'name'=>'age'));
$http->post();
