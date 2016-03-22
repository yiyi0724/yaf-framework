<?php

/**
 * redis驱动类
 * @author enychen
 * @version 1.0
 */
namespace Driver;

class Redis extends Driver {

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
	protected function __construct(array $driver) {
		// 创建redis对象
		$this->redis = new \Redis();
		// 选项设置
		foreach($driver['options'] as $key=>$option) {
			$this->redis->setOption(constant("\Redis::OPT_" . strtoupper($key)), $option);
		}
		// 持久性连接
		$this->pconnect($driver['host'], $driver['port'], $driver['timeout']);
		// 密码验证
		$driver['auth'] and $this->auth($driver['auth']);
		// 选择数据库
		$this->select($driver['dbname']);
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