<?php

namespace Core;

class Loader
{
	/**
	 * 根目录，可以设置多个
	 * @var array
	 */
	protected static $paths;
	
	/**
	 * 构造函数
	 * @param string|array $paths 查找的根目录
	 * @return void
	 */
	public static function initLoader($paths)
	{
		// 设置根目录
		static::$paths = is_array($paths) ? $paths : array($paths);
		// 设置自动加载
		spl_autoload_register('static::autoload');
	}
	
	/**
	 * 自动加载文件
	 * @param string $class 包含命名空间的类名
	 * @return void
	 */
	public static function autoload($class)
	{
		// 文件行号转换
		$class = ltrim(str_replace('\\', DIRECTORY_SEPARATOR, $class), DIRECTORY_SEPARATOR);
		// 完整文件名
		foreach(static::$paths as $path)
		{
			$file = "{$path}{$class}.php";
			//是否加载
			if(is_file($file))
			{
				require($file);
				break;
			}
		}
	}
}

/**
 * 用法
 * \Core\Loader::initLoader(string 目录或者 array(目录1, 目录2));
 */