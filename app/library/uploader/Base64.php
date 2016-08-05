<?php

namespace uploader;

class Base64 extends Uploader {

	/**
	 * base64上传字符串
	 * @var string
	 */
	protected $base64Str = NULL;

	/**
	 * 构造函数
	 * @param string $base64Str 图像编码字符串
	 * @param string $directory 保存目录
	 */
	public function __construct($base64Str, $directory) {
		 $this->setBase64Str($base64Str);
		 $this->setDirectory($directory);
	}

	/**
	 * 设置base64上传字符串
	 * @param string $base64Str 字符串
	 * @return Uploader $this 返回当前对象进行连贯操作
	 */
	public function setBase64Str($base64Str) {
		$this->base64Str = $base64Str;
		return $this;
	}

	/**
	 * 获取base64上传字符串
	 * @return string
	 */
	public function getBase64Str() {
		return $this->base64Str;
	}

	/**
	 * base64位图像上传
	 * @return string 返回文件路径
	 */
	public function upload() {
		//匹配出图片的格式
		if(!preg_match('/^(data:\s*image\/(\w+);base64,)/', $this->getBase64Str(), $result)) {
			$this->throws('没有文件被上传');
		}
		// 解析图片信息
		$this->setExt("base64.{$result[2]}");
		// 获取绝对路径文件名
		$fullFilename = $this->getFullFilename();
		// 保存图片
		file_put_contents($fullFilename, base64_decode(str_replace($result[1], NULL, $this->getBase64Str())));
		// 检查文件大小
		$this->checkFilesize($fullFilename);
		// 检查文件类型
		$this->checkAllowType($fullFilename);
		// 返回图像绝对路径
		return $fullFilename;
	}
}