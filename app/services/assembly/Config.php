<?php

/**
 * 获取配置类
 * @author enychen
 */
namespace assembly;

use \Yaf\Config\Ini;
use \Yaf\Application;

class ConfigService {

	/**
	 * 读取配置信息
	 * @static
	 * @param array $key 键名
	 * @return string|object|NULL 返回配置信息
	 */
	public final static function config($key) {
		return Application::app()->getConfig()->get($key);
	}

	/**
	 * 加载ini配置文件
	 * @static
	 * @param string $ini 文件名，不需要包含.ini后缀
	 * @param string $key 配置键名
	 * @return \Yaf\Ini 配置对象
	 */
	public final static function ini($ini) {
		static $inis = array();
		if(empty($inis[$ini])) {
			$inis[$ini] = new Ini(sprintf("%s%s.ini", CONF_PATH, $ini), \YAF\ENVIRON);
		}
		return $inis[$ini];
	}
}