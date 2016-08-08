<?php

namespace uploader;

abstract class Uploader {

	/**
	 * 远程图片上传
	 * @var string
	 */
	protected $pictureUrl = NULL;

	/**
	 * @param string $pictureUrl 远程图片地址
	 * @param string $directory 保存目录
	 */
	public function __construct($pictureUrl, $directory) {
		$this->setPictureUrl($pictureUrl);
		$this->setDirectory($directory);
	}

	/**
	 * 设置远程图像地址
	 * @param string $pictureUrl 远程图片地址
	 * @return Uploader $this 返回当前对象进行连贯操作
	 */
	protected function setPictureUrl($pictureUrl) {
		$this->pictureUrl = $pictureUrl;
		return $this;
	}

	/**
	 * 获取远程图像地址
	 * @return string
	 */
	public function getPictureUrl() {
		return $this->pictureUrl;
	}

	/**
	 * 通过url地址获取上传文件
	 * @return boolean 上传成功返回TRUE
	 */
	public function upload() {
		$origin = @file_get_contents($this->getPictureUrl(), FALSE);
		if(!$origin) {
			$this->throws('远程图片不存在');
		}
		// 解析图片信息
		$this->setExt(basename($origin));
		// 获取绝对路径文件名
		$fullFilename = $this->getFullFilename();
		// 保存图片
		file_put_contents($fullFilename, $origin);
		// 检查文件大小
		$this->checkFilesize($fullFilename);
		// 检查文件类型
		$this->checkAllowType($fullFilename);
		// 返回图像绝对路径
		return $fullFilename;
	}
}