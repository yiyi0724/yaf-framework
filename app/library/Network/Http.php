<?php

/**
 * Http请求类
 * @author enychen
 * @version 1.0
 */
namespace Network;

class Http {
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
	 * 结果解析类型
	 * @var int
	 */
	protected $decode = NULL;

	/**
	 * 构造函数
	 * @param int $timeout 超时时间，默认60秒
	 * @param bool $return 结果是否返回，如果不返回则直接输出，默认返回不输出
	 * @param bool $header　启用时会将头文件的信息作为数据流输出, 默认不输出
	 * @return void
	 */
	public function __construct($timeout = 60, $return = TRUE, $header = FALSE) {
		// 创建连接资源
		$this->curl = curl_init();
		
		// 设置头信息输出/结果返回/超时时间
		$this->setCurlopt(CURLOPT_RETURNTRANSFER, $return);
		$this->setCurlopt(CURLOPT_HEADER, $header);
		$this->setCurlopt(CURLOPT_TIMEOUT, $timeout);
	}
	
	/**
	 * 设置要请求的url地址
	 * @param string $action url地址
	 * @return void
	 */
	public function setAction($action) {
		$this->action = $action;
	}

	/**
	 * 设置结果解析
	 * @param string $decode 只支持 \Network\Http::DECODE_JSON 或者 \Network\Http::DECODE_XML
	 * @return void
	 */
	public function setDecode($decode) {
		$this->decode = $decode;
	}

	/**
	 * 设置cookie信息
	 * @param string|array $cookie cookie信息
	 * @return void
	 */
	public function setCookie($cookie) {
		if(is_array($cookie)) {
			$format = array();
			foreach($cookie as $key=>$val) {
				$format[] = "{$key}={$val}";
			}
			$cookie = implode('; ', $format);
		}
		$this->setCurlopt(CURLOPT_COOKIE, $cookie);
	}

	/**
	 * 设置curlopt的设置
	 * @param string $key CURLOPT_*设置选项,参照http://php.net/manual/zh/function.curl-setopt.php
	 * @param int|string|bool $value 值
	 * @return void
	 */
	public function setCurlopt($key, $value) {
		curl_setopt($this->curl, $key, $value);
	}

	/**
	 * 上传文件的创建方式
	 * @param string $path 文件的绝对路径
	 * @return \CURLFile|string 上传文件对象或者字符串
	 */
	public function getFile($path) {
		return class_exists('\CURLFile') ? new \CURLFile($path) : "@{$path}";
	}
	
	/**
	 * 关闭curl资源
	 * @return void
	 */
	public function close() {
		curl_close($this->curl);
	}

	/**
	 * 执行请求
	 * @param string $method 请求方法，get|post|put|delete|upload
	 * @param array $fields 要传递的参数
	 * @return bool|string 请求的结果，如果返回信息则返回请求字符串
	 */
	public function __call($method, $fields) {		
		// 读取数据
		$fields = array_shift($fields);
		
		// 进行操作
		switch($method) {
			case 'get':
				$fields and ($this->action = "{$this->action}?" . http_build_query($fields));
				break;
			case 'post':
			case 'put':
			case 'delete':
				curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, strtoupper($method));
				if($fields) {
					$fields = http_build_query($fields);
					curl_setopt($this->curl, CURLOPT_HTTPHEADER, array('Content-Length: ' . mb_strlen($fields)));
					curl_setopt($this->curl, CURLOPT_POSTFIELDS, $fields);
				}
				break;
			case 'upload':
				curl_setopt($this->curl, CURLOPT_POST, TRUE);
				curl_setopt($this->curl, CURLOPT_POSTFIELDS, $fields);
				break;
			default:
				throw new \Exception("Not Found Method Http::{$method}()", 99000);
		}
		
		// 设置请求的地址
		curl_setopt($this->curl, CURLOPT_URL, $this->action);
		
		// 执行请求
		$this->result = curl_exec($this->curl);
				
		// 操作失败
		if($this->result === FALSE) {
			throw new \Exception("Curl Exec Error", 99001);
		}
		
		// 进行解析
		$this->decode and $this->decode($this->result);
		
		// 返回结果
		return $this->result;
	}

	/**
	 * 解析结果成数组
	 * @param string $result curl的返回结果
	 * @throws \Exception
	 */
	protected function decode($result) {
		// xml解析
		if($this->decode == static::DECODE_XML) {
			$result = @simplexml_load_string($result, 'SimpleXMLElement', LIBXML_NOCDATA);
			if(!$result) {
				throw new \Exception("XML Decode Error", 99002);
			}
			$result = json_encode($result);
		}
		
		// json解析
		$result = json_decode($result, TRUE);
		if(json_last_error()) {
			throw new \Exception("Decode Error", 99002);
		}
		
		$this->result = $result;
	}
}