<?php

/**
 * 频道安全访问类
 * @author enychen
 */
namespace security;

use \storage\RedisService;

class ChannelService {

	/**
	 * 频道名称
	 * @var string
	 */
	const CACHE_KEY = 'security.channel.%s';
	
	/**
	 * 某个频道访问数自增1
	 * @static
	 * @param string $channel 频道id
	 * @param int $expire 过期时间
	 * @return void
	 */
	public static function incrChannel($channel, $expire = 900) {
		$redis = RedisService::getInstance();
		$cacheKey = sprintf(self::CACHE_KEY, $channel);

		$redis->incr($cacheKey);
		$redis->expire($cacheKey);
	}

	/**
	 * 获取频道的访问次数
	 * @param string $channel 渠道名称
	 * @return int
	 */
	public static function getChannelNumber($channel) {
		return (int)RedisService::getInstance()->get(sprintf(self::CACHE_KEY, $channel));
	}

}