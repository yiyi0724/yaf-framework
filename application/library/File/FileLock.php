<?php

/**
 * 文件锁机制类
 * @author enychen
 * @version 1.0
 */
namespace Ku;

class FileLock
{

	/**
	 * 上锁对象池
	 * @var array
	 */
	protected static $locks = array();

	/**
	 * 加锁
	 * @param string 文件名
	 */
	public static function lock($filename)
	{
		$lock = @fopen($filename, 'w+');
		@flock($lock, LOCK_EX);
		static::$locks[$filename] = $lock;
	}

	/**
	 * 解锁
	 * @param string $filename 要解锁的文件
	 * @param boolean $delete 是否删除锁文件
	 */
	public static function unlock($filename, $delete = TRUE)
	{
		if(isset(static::$locks[$filename]))
		{
			// 读取锁
			$lock = static::$locks[$filename];
			// 解锁
			@flock($lock, LOCK_UN);
			// 关闭文件
			@fclose($lock);
			// 是否删除文件
			$delete and @unlink($lock);
			// 删除锁资源
			unset(static::$locks[$filename]);
		}		
	}

	/**
	 * 对已经上锁的全部资源进行解锁
	 * @param boolean $delete 是否删除锁文件
	 */
	public static function unlocks($delete = TRUE)
	{
		foreach(static::$locks as $key=>$lock)
		{
			// 解锁
			@flock($lock, LOCK_UN);
			// 关闭文件
			@fclose($fp);
			// 是否删除文件
			$delete and @unlink($fp);
			// 清空资源
			unset(static::$locks[$key]);
		}
	}
}