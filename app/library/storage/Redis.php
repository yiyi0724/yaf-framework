<?php

/**
 * redis存储类
 * @author enychen
 * @version 1.0
 */
namespace storage;

class Redis extends Adapter {

	/**
	 * 单例对象
	 * @var Redis
	 */
	protected static $pool;

	/**
	 * redis对象
	 * @var \Redis
	 */
	protected $redis;

	/**
	 * 创建对象
	 * @param array $driver 配置数组 host | port | timeout | auth | dbname | options
	 * @throws \RedisException
	 * @return void
	 */
	protected function __construct($host, $port, $db, $timeout, $auth, array $options) {
		// 创建redis对象
		$this->redis = new \Redis();
		// 选项设置
		foreach($options as $key=>$option) {
			$this->redis->setOption(constant("\Redis::OPT_" . strtoupper($key)), $option);
		}
		// 持久性连接
		$this->pconnect($host, $port, $timeout);
		// 密码验证
		$auth and $this->auth($auth);
		// 选择数据库
		$this->select($db);
	}

	/**
	 * 单例获取redis
	 */
	public static function getInstance($host, $port, $db, $timeout, $auth, $options) {
		if(empty(static::$pool["{$host}:{$port}"])) {
			static::$pool["{$host}:{$port}"] = new static($host, $port, $db, $timeout, $auth, $options);
		}
		
		return static::$pool["{$host}:{$port}"];
	}

	/**
	 * 设置键值
	 * @param string $key 键
	 * @param mixed $value 值
	 * @param int $expire 过期时间，默认为0表示不过期
	 * @return bool 是否设置成功
	 */
	public function set($key, $value, $expire = 0) {
		return $this->redis->set($key, $value) && $expire && $this->redis->expire($key, $expire);
	}

	/**
	 * 设置键值
	 * @param string $key 键
	 * @param mixed $default 如果找不到这个键则删除
	 * @return bool 是否设置成功
	 */
	public function get($key, $default = NULL) {
		$value = $this->redis->get($key);
		return $value === FALSE ? $default : $value;
	}

	public function del($key) {
		return $this->redis->del($key);
	}

	/**
	 * 静态调用方式
	 * @param string $method 方法名
	 * @param array $args 参数
	 * @return mixed
	 */
	public function __call($method, $args) {
		try {
			return call_user_func_array(array($this->redis, $method), $args);
		} catch(\RedisException $e) {
			return FALSE;
		}
	}
}