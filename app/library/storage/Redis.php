<?php

/**
 * redis存储类
 * @author enychen
 */
namespace storage;

class Redis extends Adapter {

	/**
	 * 对象池
	 * @var array
	 */
	protected static $pool;

	/**
	 * redis对象
	 * @var \Redis
	 */
	protected $redis;

	/**
	 * 构造函数
	 * @param string $host 服务器地址
	 * @param string $port 服务器端口
	 * @param int $timeout 连接超时时间
	 * @param string $auth 密码
	 * @param string $options 可选配置
	 * @throws \RedisException
	 */
	protected function __construct($host, $port, $timeout, $auth, array $options) {
		// 创建redis对象
		$this->redis = new \Redis();
		// 选项设置
		foreach($options as $key=>$option) {
			$this->redis->setOption(constant("\Redis::OPT_" . strtoupper($key)), $option);
		}
		// 持久性连接
		$this->redis->pconnect($host, $port, $timeout);
		// 密码验证
		$auth and $this->redis->auth($auth);
	}

	/**
	 * 获取原生redis对象
	 * @return \Redis
	 */
	public function getRedis() {
		return $this->redis;
	}

	/**
	 * 获取单例对象
	 * @param string $host 服务器地址
	 * @param string $port 服务器端口
	 * @param int $timeout 连接超时时间
	 * @param string $auth 密码
	 * @param string $options 可选配置
	 * @return Redis
	 */
	public static function getInstance($host, $port, $timeout, $auth, $options) {
		$key = sprintf("%s:%s", $host, $port);
		if(empty(static::$pool[$key])) {
			static::$pool[$key] = new static($host, $port, $timeout, $auth, $options);
		}
		return static::$pool[$key];
	}

	/**
	 * 设置值并设置过期时间
	 * @param string $key 键名
	 * @param string $value 值
	 * @param int $expire 过期时间
	 * @return boolean 只会TRUE
	 */
	public function setWithExpire($key, $value, $expire = 0) {
		$this->getRedis()->set($key, $value);
		$expire and $this->getRedis()->setExpire($key, $expire);
		return TRUE;
	}

	/**
	 * 获取并且检查过期时间
	 * @param string $key 键名
	 * @param int $expire 过期时间,此参数无用兼容基类而已
	 * @return mixed 找到返回具体值，找不到返回FALSE
	 */
	public function getWithExpire($key, $expire = 0) {
		return $this->getRedis()->get($key);
	}

	/**
	 * 回调原生redis的方法
	 * @param string $method 方法名
	 * @param array $args 参数
	 * @return mixed
	 */
	public function __call($method, $args) {
		return call_user_func_array(array($this->getRedis(), $method), $args);
	}
}