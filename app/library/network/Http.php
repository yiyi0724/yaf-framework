<?php

/**
 * Http请求类
 * @author enychen
 * @version 1.0
 */
namespace network;

class Http {

	/**
	 * json解析
	 * @var int
	 */
	const DECODE_JSON = 1;

	/**
	 * xml解析
	 * @var int
	 */
	const DECODE_XML = 2;

	/**
	 * 请求地址
	 * @var string
	 */
	protected $url = NULL;

	/**
	 * 结果解析类型
	 * @var int
	 */
	protected $decode = NULL;

	/**
	 * curl的选项信息
	 * @var array
	 */
	protected $curlOpt = array();

	/**
	 * 默认超时时间
	 * @var int
	 */
	protected $timeout = 5;

	/**
	 * 默认结果返回
	 * @var boolean
	 */
	protected $isReturn = TRUE;

	/**
	 * 默认请求头信息不作为数据流输出
	 * @var boolean
	 */
	protected $isUseheader = FALSE;

	/**
	 * 请求结果，原始返回数据
	 * @var string
	 */
	protected $originResult = NULL;

	/**
	 * 设置要请求的url地址
	 * @param string $url url地址
	 * @return Http $this 返回当前对象进行连贯操作
	 */
	public function setUrl($url) {
		$this->url = $url;
		return $this;
	}

	/**
	 * 获取要请求的url地址
	 * @return string
	 * @throws \Exception
	 */
	public function getUrl() {
		if(!$this->url) {
			throw new \Exception('请设置请求地址');
		}
		return $this->url;
	}

	/**
	 * 设置结果解析
	 * @param int $decode 只支持 \network\Http::DECODE_JSON 或者 \network\Http::DECODE_XML
	 * @return Http $this 返回当前对象进行连贯操作
	 * @throws \Exception
	 */
	public function setDecode($decode) {
		if(!in_array($decode, array(static::DECODE_JSON, static::DECODE_XML))) {
			throw new \Exception('设置结果解析方式有误');
		}
		$this->decode = $decode;
		return $this;
	}
	
	/**
	 * 获取结果解析信息
	 * @return int
	 */
	public function getDecode() {
		return $this->decode;
	}

	/**
	 * 设置curlopt的设置
	 * @param string $key CURLOPT_*设置选项,参照http://php.net/manual/zh/function.curl-setopt.php
	 * @param int|string|bool $value 对应的值
	 * @return Http $this 返回当前对象进行连贯操作
	 */
	public function setCurlOpt($key, $value) {
		$this->curlOpt[$key] = $value;
		return $this;
	}

	/**
	 * 获取curlopt设置
	 * @return array
	 */
	public function getCurlOpt() {
		return $this->curlOpt;
	}

	/**
	 * 设置超时时间
	 * @param int $timeout 超时时间，单位：秒
	 * @return Http $this 返回当前对象进行连贯操作
	 */
	public function setTimeout($timeout) {
		$this->timeout = $timeout;
		return $this;
	}

	/**
	 * 获取超时时间
	 * @return int
	 */
	public function getTimeout() {
		return $this->timeout;
	}

	/**
	 * 设置是否返回数据
	 * @param boolean $isReturn 是否返回数据
	 * @return Http $this 返回当前对象进行连贯操作
	 */
	public function setIsReturn($isReturn) {
		$this->isReturn = $isReturn;
		return $this;
	}

	/**
	 * 获取是否返回数据
	 * @return boolean
	 */
	public function getIsReturn() {
		return $this->isReturn;
	}

	/**
	 * 设置是否使用将头信息当成数据
	 * @param boolean $isUserHeader 是否使用头信息
	 * @return Http $this 返回当前对象进行连贯操作
	 */
	public function setIsUserHeader($isUserHeader) {
		$this->isUseheader = $isUseheader;
		return $this;
	}

	/**
	 * 获取是否使用将头信息当成数据
	 * @return boolean
	 */
	public function getIsUserHeader() {
		return $this->isUseheader;
	}

	/**
	 * 设置请求结果原始数据
	 * @param string $originResult 请求的结果数据信息
	 * @return Http $this 返回当前对象进行连贯操作
	 */
	protected function setOriginResult($originResult) {
		$this->originResult = $originResult;
		return $this;
	}

	/**
	 * 获取请求结果原始数据
	 * @return string
	 */
	public function getOriginResult() {
		return $this->originResult;
	}

	/**
	 * 设置cookie信息
	 * @param string|array $cookie cookie信息
	 * @return Http $this 返回当前对象进行连贯操作
	 */
	public function setCookie($cookie) {
		if(is_array($cookie)) {
			$format = array();
			foreach($cookie as $key=>$val) {
				$format[] = "{$key}={$val}";
			}
			$cookie = implode('; ', $format);
		}		
		$this->setCurlOpt(CURLOPT_COOKIE, $cookie);

		return $this;
	}

	/**
	 * 执行get请求
	 * @param array|string $send 附加参数
	 * @return array|string|boolean 根据情况返回信息
	 * @throws \Exception
	 */
	public function get($send = array()) {
		// 设置请求的地址
		$send = is_array($send) ? urldecode(http_build_query($send)) : $send;
		$url = $send ? sprintf('%s?%s', $this->getUrl(), $send) : $this->getUrl();
		$this->setCurlOpt(CURLOPT_URL, $url);

		// 执行请求
		return $this->send();
	}

	/**
	 * 执行post请求
	 * @param array|string $send 附加参数
	 * @return array|string|boolean 根据情况返回信息
	 * @throws \Exception
	 */
	public function post($send = array()) {
		// 设置curl信息
		$this->setCurlOpt(CURLOPT_URL, $this->getUrl());
		$this->setCurlOpt(CURLOPT_POST, TRUE);
		$this->setCurlOpt(CURLOPT_POSTFIELDS, $send);

		// 执行请求
		return $this->send();
	}

	/**
	 * 执行put请求
	 * @param array|string $send 附加参数
	 * @return array|string|boolean 根据情况返回信息
	 * @throws \Exception
	 */
	public function put($send = array()) {
		// 设置curl信息
		$this->setCurlOpt(CURLOPT_URL, $this->getUrl());
		$this->setCurlOpt(CURLOPT_CUSTOMREQUEST, 'PUT');

		// 可选参数信息
		if($send) {
			$send = is_array($send) ? http_build_query($send) : $send;
			$this->setCurlOpt(CURLOPT_POSTFIELDS, $send);
			$this->setCurlOpt(CURLOPT_HTTPHEADER, array(sprintf('Content-Length: %d', strlen($send))));
		}

		// 执行请求
		return $this->send();
	}

	/**
	 * 执行delete请求
	 * @param array|string $send 附加参数
	 * @return array|string|boolean 根据情况返回信息
	 * @throws \Exception
	 */
	public function delete($send = array()) {
		// 设置curl信息
		$this->setCurlOpt(CURLOPT_URL, $this->getUrl());
		$this->setCurlOpt(CURLOPT_CUSTOMREQUEST, 'DELETE');

		// 可选参数信息
		if($send) {
			$send = is_array($send) ? http_build_query($send) : $send;
			$this->setCurlOpt(CURLOPT_POSTFIELDS, $send);
			$this->setCurlOpt(CURLOPT_HTTPHEADER, array(sprintf('Content-Length: %d', strlen($send))));
		}

		// 执行请求
		return $this->send();
	}

	/**
	 * 执行upload请求
	 * @param array $send 附加参数
	 * @return array|string|boolean 根据情况返回信息
	 * @throws \Exception
	 */
	public function upload(array $send) {
		// 设置curl选项
		$this->setCurlOpt(CURLOPT_URL, $this->getUrl());
		$this->setCurlOpt(CURLOPT_POST, TRUE);
		$this->setCurlOpt(CURLOPT_POSTFIELDS, $this->toCURLFile($send));

		return $this->send();
	}

	/**
	 * 兼容上传（兼容@和CURLFile）
	 * @param array $send 待上传的文件
	 * @return array
	 */
	protected function toCURLFile($send) {
		if(class_exists('\CURLFile')) {
			foreach($send as $key=>$value) {
				if(is_array($send[$key])) {
					// 递归检查
					$send[$key] = $this->toCURLFile($send[$key]);
				} else if(substr($value, 0, 1) == '@') {
					// 存在要上传的文件
					$send[$key] = new \CURLFile(substr($value, 1));
				}
			}
		}

		return $send;
	}
	
	/**
	 * 公共请求方式
	 * @throws \Exception
	 * @return mixed
	 */
	protected function send() {
		// 创建资源对象
		$curl = curl_init();

		// 设置请求的选项信息
		$this->setCurlOpt(CURLOPT_RETURNTRANSFER, $this->getIsReturn());
		$this->setCurlOpt(CURLOPT_HEADER, $this->getIsUserHeader());
		$this->setCurlOpt(CURLOPT_TIMEOUT, $this->getTimeout());
		
		// 设置curl选项
		curl_setopt_array($curl, $this->getCurlOpt());
		
		// 执行请求并关闭
		$result = curl_exec($curl);
		if($result === FALSE) {
			$curlError = curl_error($curl);
			$curlNo = curl_errno($curl);
		}
		// 关闭curl
		curl_close($curl);
		// 操作失败
		if($result === FALSE) {
			throw new \Exception($curlError, $curlNo);
		}

		// 保存原始数据
		$this->setOriginResult($result);

		// 进行解析
		if($decode = $this->getDecode()) {
			// xml解析
			if($decode == static::DECODE_XML) {
				$result = @simplexml_load_string($result, 'SimpleXMLElement', LIBXML_NOCDATA);
				if(!$result) {
					throw new \Exception('xml数据解析失败');
				}
				$result = json_encode($result);
			}			
			// json解析
			$result = json_decode($result, TRUE);
			if(json_last_error() || !is_array($result)) {
				throw new \Exception('数据解析失败');
			}
		}

		// 返回结果
		return $result;
	}
}