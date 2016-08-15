<?php

/**
 * cookie类
 * @author enychen
 */
namespace network;

class Cookie {

	/**
	 * cookie名称
	 * @var string
	 */
	protected $name = NULL;

	/**
	 * cookie值
	 * @var string
	 */
	protected $value = NULL;

	/**
	 * cookie过期时间
	 * @var int
	 */
	protected $expire = 86400;

	/**
	 * cookie允许的目录
	 * @var string
	 */
	protected $path = '/';

	/**
	 * cookie所属的域名
	 * @var string
	 */	
	protected $domain = NULL;

	/**
	 * cookie是否使用https
	 * @var boolean
	 */
	protected $secure = FALSE;

	/**
	 * cookie是否字段
	 * @var string
	 */
	protected $httpOnly = TRUE;

	/**
	 * 设置cookie名称
	 * @param string $name cookie名称
	 * @return Cookie $this 返回当前对象进行连贯操作
	 */
	public function setName($name) {
		$this->name = $name;
		return $this;
	}

	/**
	 * 获取cookie名称
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * 设置cookie值
	 * @param string $value cookie值
	 * @return Cookie $this 返回当前对象进行连贯操作
	 */
	public function setValue($value) {
		$this->value = $value;
		return $this;
	}

	/**
	 * 获取cookie值
	 * @return string
	 */
	public function getValue() {
		return $this->value;
	}

	/**
	 * 设置过期时间
	 * @param int $expire 过期时间
	 * @return Cookie $this 返回当前对象进行连贯操作
	 */
	public function setExpire($expire) {
		$this->expire = $expire;
		return $this;
	}

	/**
	 * 获取过期时间
	 * @return int
	 */
	public function getExpire() {
		return $this->expire;
	}

	/**
	 * 设置所属目录
	 * @param string $path 所属目录
	 * @return Cookie $this 返回当前对象进行连贯操作
	 */
	public function setPath($path) {
		$this->path = $path;
		return $this;
	}

	/**
	 * 获取所属目录
	 * @return string
	 */
	public function getPath() {
		return $this->path;
	}

	/**
	 * 设置所属域名
	 * @param string $domain 所属域名
	 * @param Cookie $this 返回当前对象进行连贯操作
	 */
	public function setDomain($domain) {
		$this->domain = $domain;
		return $this;
	}

	/**
	 * 获取所属域名
	 * @return string
	 */
	public function getDomain() {
		return $this->domain;
	}

	/**
	 * 设置是否使用https
	 * @param boolean $secure 是否使用https
	 * @return Cookie $this 返回当前对象进行连贯操作
	 */
	public function setSecure($secure) {
		$this->secure = $secure;
		return $this;
	}

	/**
	 * 获取是否使用https
	 * @return boolean
	 */
	public function getSecure() {
		return $this->secure;
	}

	/**
	 * 设置cookie是否只读
	 * @param boolean $httpOnly 是否只读
	 * @return Cookie $this 返回当前对象进行连贯操作
	 */
	public function setHttpOnly($httpOnly) {
		$this->httpOnly = $httpOnly;
		return $this;
	}

	/**
	 * 获取cookie是否只读
	 * @return boolean
	 */
	public function getHttpOnly() {
		return $this->httpOnly;
	}

	/**
	 * 抛出异常信息
	 * @param string $message 异常信息
	 * @param int $code 异常码
	 * @return void
	 * @throws \Exception
	 */
	protected function throws($message, $code = 0) {
		throw new \Exception($message, $code);
	}

	/**
	 * 增加cookie
	 * @return boolean 设置成功返回TRUE，否则返回FALSE
	 */
	public function add() {
		// 必备参数检查
		if(!$this->getName()) {
			$this->throws('请设置cookie名称');
		}
		if(!$this->getValue()) {
			$this->throws('请设置cookie值');
		}
		if(!$this->getDomain()) {
			$this->throws('请设置cookie域名');
		}

		// 要设置的cookie信息
		$name = $this->getName();
		$value = $this->getValue();
		$expire = time() + $this->getExpire();
		$path = $this->getPath();
		$domain = $this->getDomain();
		$secure = isset($_SERVER['REQUEST_SCHEME']) && (!strcasecmp($_SERVER['REQUEST_SCHEME'], 'https'));
		$httpOnly = $this->gethttpOnly();

		// 设置cookie
		return setcookie($name, $value, $expire, $path, $domain, $secure, $httpOnly);
	}

	/**
	 * 删除cookie
	 * @return boolean 删除成功后返回TRUE，否则返回FALSE
	 */
	public function delete() {
		// 必备参数检查
		if(!$this->getName()) {
			$this->throws('请设置cookie名称');
		}
		if(!$this->getDomain()) {
			$this->throws('请设置cookie域名');
		}

		// 要删除的必备信息
		$name = $this->getName();
		$path = $this->getPath();
		$expire = time() - $this->getExpire();
		$domain = $this->getDomain();
		$secure = isset($_SERVER['REQUEST_SCHEME']) && (!strcasecmp($_SERVER['REQUEST_SCHEME'], 'https'));
		$httpOnly = $this->gethttpOnly();

		// 进行删除
		return setcookie($name, NULL, $expire, $path, $domain, $secure, $httpOnly);
	}
}