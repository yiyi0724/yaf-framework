<?php

/**
 * Http请求类
 * @author chenxb
 * @version 1.3
 *
 * @example
 * $http = new Http($url);
 * 
 * 可选的方法:
 * 1. 结果自动解析: $http->setDecode(string $type); // $type的可选值: json 或者 xml
 * 2. 设置要发送的cookie信息: $http->setCookie($cookie); $cookie的形式: key=value; key=value 或者 array('key'=>'value')
 * 3. 设置要发送的header信息: $http->setHeader($headers); $headers 是一个字符串 或者 array('xxx', 'xxx')的格式
 * 4. 如果要上传文件,请使用: $data['upload'] = $http->getFile(string 文件名); 由于php版本的问题,我封装了这个解决方法
 * 5. 设置CURLOPT选项: $http->setCurlOpt(CURLOPT_*, $value);
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

class Http {

	/**
	 * 请求地址
	 * @var string
	 */
	protected $action = Null;

	/**
	 * 连接资源
	 * @var resource
	 */
	protected $curl = Null;

	/**
	 * 错误信息
	 * @var string
	 */
	protected $error = Null;

	/**
	 * 请求结果
	 * @var string
	 */
	protected $result = Null;

	/**
	 * 结果解析
	 * @var bool
	 */
	protected $decodeType = Null;

	/**
	 * 构造函数
	 * @param string $action 要请求的url地址
	 */
	public function __construct($action){
		// 创建连接资源
		$this->curl = curl_init();
		// url地址
		$this->action = $action;
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
	public function __call($method, $fields){
		$fields = $this->setFields($fields, $method != 'upload');
		switch($method){
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
				curl_setopt($this->curl, CURLOPT_POST, true);
				curl_setopt($this->curl, CURLOPT_POSTFIELDS, $fields);
				break;
			default:
				trigger_error("Fatal Error: NOT FOUND METHOD Http::{$mthod}()");
		}
		
		try{
			// 设置请求的地址
			curl_setopt($this->curl, CURLOPT_URL, $this->action);
			// 执行请求
			$this->result = curl_exec($this->curl);
			// 进行json解析
			$this->decodeType and call_user_func(array($this, "{$this->decodeType}Decode"), $this->result);
		}
		catch(\Exception $e){
			$this->result = null;
			$this->error = $e->getMessage();
		}
		
		// 返回结果和错误
		return array($this->result, $this->error);
	}

	/**
	 * 处理参数
	 * @param array $fields 参数
	 * @param bool $httpBuild 是否数组转换
	 * @return array|string
	 */
	protected function setFields($fields, $httpBuild = true){
		isset($fields[0]) and ($fields = $fields[0]);
		return $httpBuild ? http_build_query($fields) : $fields;
	}

	/**
	 * json解析结果成数组
	 * @param string $result curl的返回结果
	 * @throws \Exception
	 */
	protected function jsonDecode($result){
		$this->result = json_decode($this->result, True);
		if(json_last_error()){
			throw new \Exception("JSON DECODE ERROR, ORIGIN DATA:{$result}");
		}
	}

	/**
	 * xml解析结果成数组
	 * @param string $result curl的返回结果
	 * @throws \Exception
	 */
	protected function xmlDecode($result){
		// 解析失败,错误抑制,抛出异常
		$this->result = @simplexml_load_string($this->result, 'SimpleXMLElement', LIBXML_NOCDATA);		
		if(!$this->result){
			throw new \Exception("XML DECODE ERROR, ORIGIN DATA:{$result}");
		}
		$this->result = json_decode(json_encode($this->result), True);
	}

	/**
	 * 设置头信息
	 * @param array|string $header 头信息数组
	 */
	public function setHeader($headers){
		$headers = is_array($headers) ? $headers : array($headers);
		curl_setopt($this->curl, CURLOPT_HTTPHEADER, $headers);
	}

	/**
	 * 设置cookie
	 * @param string|array $cookie cookie信息
	 */
	public function setCookie($origin){
		$cookie = $origin;
		
		// 数组转成字符串
		if(is_array($origin)){
			foreach($origin as $key=>$val){
				$cookie[] = "{$key}=>{$val}";
			}
			$cookie = implode('; ', $cookie);
		}
		
		curl_setopt($this->curl, CURLOPT_COOKIE, $cookie);
	}

	/**
	 * 设置curlopt的设置
	 * @param string $key CURLOPT_*设置选项
	 * @param mixed $value 值
	 */
	public function setCurlopt($key, $value){
		curl_setopt($this->curl, $key, $value);
	}

	/**
	 * 结果解析
	 * @param string $type json或者xml
	 */
	public function setDecode($type){
		$this->decodeType = $type;
	}

	/**
	 * 上传文件的创建方式
	 * @param string $path 文件的绝对路径
	 * @return mixed
	 */
	public function getFile($path){
		return class_exists('\CURLFile') ? new \CURLFile($path) : "@{$path}";
	}

	/**
	 * 关闭连接
	 */
	public function close(){
		curl_close($this->curl);
	}
}