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
	 * @param array $driver 配置数组 host | port | timeout | auth | db | options
	 * @throws \RedisException
	 * @return void
	 */
	protected function __construct(array $driver) {
		try {
			// 创建redis对象
			$this->redis = new \Redis();
			// 选项设置
			foreach($driver['options'] as $key=>$option) {
				$this->redis->setOption(constant("\Redis::OPT_" . strtoupper($key)), $option);
			}
			// 持久性连接
			$this->redis->pconnect($driver['host'], $driver['port'], $driver['timeout']);
			// 选择数据库
			$this->redis->select($driver['dbname']);
			// 密码
			$driver['auth'] and $this->redis->auth($driver['auth']);
		} catch(\Exception $e) {
			
		}
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
		} catch(\Exception $e) {
			return FALSE;
		}
		
	}
}