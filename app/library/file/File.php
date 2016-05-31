<?php

/**
 * 文件类型
 */
namespace File;

class File {
	
	protected $fileinfo = NULL;
	
	/**
	 * 文件
	 */
	public function __construct($file) {
		$this->fileinfo = pathinfo($file);
	}
	
	/**
	 * 获得文件扩展名
	 */
	public function getExtension() {
		
	}
}