<?php

/**
 * 文件锁机制类
 * @author enychen
 * @version 1.0
 */
namespace File;

class FileLock {

	/**
	 * 锁文件目录
	 * @var string
	 */
	private $path = './locks/';
	
	/**
	 * 
	 * @var unknown
	 */
	private $lockFile = NULL;
	
	/**
	 * 所有锁文件池
	 * @var string
	 */
	private $lockFiles = array();
	
	private $errorCode = 0;
	
	private $errorMsg = NULL;
	
	/**
	 * 用于设置成员属性($path)
	 * @param  string $key 成员属性名(不区分大小写)
	 * @param  mixed  $val 为成员属性设置的值
	 * @return \File\FileLock 返回自己对象$this，可以用于连贯操作
	 */
	public function set($key, $val) {
		$key = strtolower($key);
		if(in_array($key, get_class_vars(get_class($this)))) {
			$this->setOption($key, $val);
		}
		
		return $this;
	}
	
	/**
	 * 为单个成员属性设置值
	 * @param string $key 键
	 * @param string $val 值
	 * @return void
	 */
	private function setOption($key, $val) {
		$this->$key = $val;
	}
	
	private function getError() {
		switch($this->errorCode) {
			case -1:
				$str = '文件上锁失败';
				break;
		}
	}
	
	private function setlockFile($filename) {
		$this->setOption('lockFile', rtrim($path, '/') . '/' . $filename);
	}
	

	/**
	 * 加锁
	 * @param string $filename 文件名
	 * @return bool 加锁成功返回TRUE
	 */
	public function lock($filename, $return = TRUE) {
		// 打开文件
		$this->setlockFile($filename);
		$lock = @fopen($this->lockFile, 'w+');
		if(!$lock) {
			$this->setOption('errorCode', -1);
			return FALSE;
		}
		
		// 对文件进行上锁
		$return = @flock($lock, LOCK_EX);
		if($return) {
			$this->lockFiles[$this->lockFile] = $lock;
			unset($lock);
		}
		
		return $return;
	}

	/**
	 * 解锁
	 * @param string $filename 要解锁的文件
	 * @param boolean $delete 是否删除锁文件
	 */
	public function unlock($filename, $delete = TRUE) {
		$this->setlockFile($filename);
		if(isset($this->lockFiles[$this->lockFile])) {
			// 读取锁
			$lock = $this->lockFiles[$this->lockFile];
			// 解锁
			@flock($lock, LOCK_UN);
			// 关闭文件
			@fclose($lock);
			// 是否删除文件
			$delete and @unlink($lock);
			// 删除锁资源
			unset($this->lockFiles[$this->lockFile], $lock);
		}
	}

	/**
	 * 对已经上锁的全部资源进行解锁
	 * @param boolean $delete 是否删除锁文件
	 */
	public static function unlocks($delete = TRUE) {
		foreach($this->lockFiles as $key=>$lock) {
			// 解锁
			@flock($lock, LOCK_UN);
			// 关闭文件
			@fclose($lock);
			// 是否删除文件
			$delete and @unlink($lock);
		}
		// 清空资源
		static::$locks = array();
	}
}