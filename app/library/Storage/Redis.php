<?php

/**
 * redis驱动类
 * @author enychen
 * @version 1.0
 */
namespace Storage;

class Redis extends Assembly {

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