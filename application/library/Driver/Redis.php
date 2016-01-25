<?php

/**
 * redis驱动类
 * @author enychen
 * @version 1.0
 */
namespace Driver;

class Redis
{
	/**
	 * 加载单例模式
	 * @var \Traits\Singleton
	 */
	use \Traits\Singleton;

	/**
	 * 当前的redis对象
	 * @var \Redis
	 */
	protected $redis;

	/**
	 * 创建对象
	 * @param array $driver 配置数组 host | port | timeout | auth | db | options
	 * @throws \RedisException
	 */
	protected function create($driver)
	{
		// 创建redis对象
		$this->redis = new \Redis();
		// 连接redis
		if($this->redis->pconnect($driver['host'], $driver['port'], $driver['timeout']))
		{
			// 是否需要验证密码
			$driver['auth'] and $this->redis->auth($driver['auth']);
			// 是否需要选择数据库
			$driver['db'] and $this->redis->select($driver['db']);
			// 选项设置
			if(isset($driver['options']))
			{
				foreach($driver['options'] as $key=>$option)
				{
					$this->redis->setOption(constant("\Redis::OPT_".strtoupper($key)), $option);
				}
			}
		}
		else
		{
			// 连接失败抛出错误
			throw new \RedisException("Redis Connection Error: {$driver['host']}:{$driver['port']}");
		}
	}

	/**
	 * 静态调用方式
	 * @param string $method 方法名
	 * @param array $args 参数
	 */
	public function __call($method, $args)
	{
		return call_user_func_array([$this->redis, $method], $args);
	}
}