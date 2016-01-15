<?php

/**
 * 文件锁机制类
 * @author enychen
 */
namespace File;

class Concurrent
{

	/**
	 * 加锁的文件
	 * @var string
	 */
	protected static $filename;

	/**
	 * 加锁文件资源
	 * @var Resource
	 */
	protected static $fp;

	/**
	 * 加锁
	 * @param string 文件名
	 */
	public static function lock($filename)
	{
		// 完整文件名
		self::$filename = $filename;
		// 打开文件,不存在的时候则尝试创建
		self::$fp = fopen(self::$filename, 'w+');
		// 文件加锁
		@flock(self::$fp, LOCK_EX);
	}

	/**
	 * 解锁
	 * @param boolean 是否删除锁文件
	 */
	public static function unlock($delete = TRUE)
	{
		// 解锁
		@flock(self::$fp, LOCK_UN);
		// 关闭文件
		fclose(self::$fp);
		// 是否删除文件
		$delete and @unlink(self::$filename);
	}
}