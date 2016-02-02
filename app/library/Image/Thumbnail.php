<?php

/**
 * 生成缩略图,仅支持gif|jpeg|png|wbmp|webp|xbm|xmp图像的生成
 * @author enychen
 * @version 1.0
 * 
 * @example
 * $thumbnail = new \Image\Thumbnail();
 * 
 * 1. 载入原图:			$result = $thumbnail->loadSrc('/home/eny/Downloads/iphone6s.jpg');	// 返回是否支持图像生成
 * 2. 设置缩略图大小: 	$thumbnail->setDistSize(205, 190);
 * 3. 设置缩略图格式:	$thumbnail->setDistType($type); //格式只支持上面列出,不设置默认和原图相同格式,注意jpg请写成jpeg
 * 3. 生成缩略图:		$thumbnail->create('/home/eny/Downloads/iphone6s_205.jpg');
 * 4. 销毁资源:			$thumbnail->destroy();
 */
namespace Image;

class Thumbnail
{

	/**
	 * 支持缩略图的格式
	 * @var array
	 */
	protected $types = array(1=>'gif', 2=>'jpeg', 3=>'png', 4=>'wbmp', 5=>'webp', 6=>'xbm', 7=>'xpm');

	/**
	 * 图片的信息
	 * @var array
	 */
	protected $info = array();

	/**
	 * 源图片载入并且图像分析
	 * @param string $src 源文件
	 */
	public function loadSrc($src)
	{
		// 图像分析
		$imageInfo = getimagesize($src);		
		if(!in_array($imageInfo[2], $this->types)) {
			return FALSE;
		}
		// 图像信息
		$this->info['srcWidth'] = $this->info['distWidth']  = $imageInfo[0];
		$this->info['srcHeight'] = $this->info['distHeight'] = $imageInfo[1];
		$method = "imagecreatefrom{$this->types[$imageInfo[2]]}";
		$this->info['srcImage'] = $method($src);
		$this->info['saveMethod'] = "image{$this->types[$imageInfo[2]]}";
		
		return TRUE;
	}

	/**
	 * 设置目标图片保存的格式
	 * @param string $type 图片格式,注意jpg请写成jpeg
	 */
	public function setDistType($type)
	{
		$this->info['saveMethod'] = "image{$type}";
	}

	/**
	 * 设置缩略图的长度和高度
	 * @param int $width
	 * @param int $height
	 */
	public function setDistSize($width, $height)
	{
		$this->info['distWidth'] = $width;
		$this->info['distHeight'] = $height;
	}

	/**
	 * 创建缩略图
	 * @param string $dist 缩略图绝对路径,如/path/to/file.jpg
	 * @param bool 是否创建成功
	 */
	public function create($dist)
	{
		// 释放变量
		extract($this->info);
		// 创建新的画布
		$distImage = imagecreatetruecolor($distWidth, $distHeight);
		// 完整拷贝
		imagecopyresampled($distImage, $srcImage, 0, 0, 0, 0, $distWidth, $distHeight, $srcWidth, $srcHeight);
		// 生成缩略图
		$result = $saveMethod($distImage, $dist);
		// 销毁目标资源
		imagedestroy($distImage);
		// 返回结果
		return $result;
	}

	/**
	 * 销毁原图资源
	 */
	public function destroy()
	{
		imagedestroy($this->info['srcImage']);
	}
}
