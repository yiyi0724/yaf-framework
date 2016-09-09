<?php

/**
 * 获取redis对象逻辑类
 * @author enychen
 */
namespace storage;

use \common\ConfigService;

class RedisService {
	
	/**
	 * 获取redis对象
	 * @param int $db 几号数据库，默认0
	 * @param string $adapter 适配器名称，默认master
	 * @return \storage\Redis redis的封装对象
	 */
	public final static function getInstance($adapter = 'master') {
		$c = ConfigService::ini('driver', sprintf("redis.%s", $adapter));
		$redis = \storage\Redis::getInstance($c->host, $c->port, $c->auth, $c->timeout, $c->options->toArray());
		$redis->select($c->db);
		return $redis;
	}
}