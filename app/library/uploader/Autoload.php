<?php

/**
 * 微信sdk全局自动加载函数
 * 使用wxsdk前请先require此文件，之后就可以使用所有的微信全局类
 * @author enychen
 * @desc 2016年8月24日
 * @vesion 1.0
 */

spl_autoload_register(function($class) {
	require(sprintf("%s/%s.php", dirname(__DIR__), str_replace('\\', '/', $class)));
});