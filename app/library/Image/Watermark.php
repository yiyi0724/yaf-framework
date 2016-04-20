<?php

namespace Image;

class Watermark extends Image {
	
	/**
	 * 图片保存的目录
	 * @var string
	 */
	protected $path = './watermark/';
	
	/**
	 * 水印图片
	 * @var string
	 */
	protected $waterfilename;
	
	protected $errorNotify = array('-7'=>'图片太大');
	
	
	/**
	 * 增加水印
	 */
	public function increase() {
		// 检查文件是否存在
		if(!$this->isWritable()) {
			$this->setOption('errorMsg', $this->getError());
			return FALSE;
		}

		// 设置图片信息
		if(!$this->setSrcFilename()) {
			$this->setOption('errorMsg', $this->getError());
			return FALSE;
		}
		
		// 设置目标图片信息
		if(!$this->setWaterInfo()) {
			$this->setOption('errorMsg', $this->getError());
			return FALSE;
		}
	
		// 生成缩略图
		if(!$this->copy()) {
			$this->setOption('errorMsg', $this->getError());
			return FALSE;
		}
		
		return TRUE;
	}
}