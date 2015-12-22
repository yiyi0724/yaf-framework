<?php
/**
 * 缩略图
 * @author chenxb
 * 
 * @example
 * $image = new \Image\Thumbnail();
 * 	$image->loadSrc('/home/eny/Downloads/iphone6s.jpg')
		  ->setDistSize(205, 190)
			  ->create('/home/eny/Downloads/iphone6s_205.png', 'png')
			  ->setDistSize(102, 95)
			  ->create('/home/eny/Downloads/iphone6s_205.jpg')
			  ->destroy();
 */

namespace Image;

class Thumbnail
{	
	/**
	 * 源图片载入
	 * @param string $src 源文件
	 */
	public function loadSrc($src)
	{
		$srcMethod = array(1=>'gif', 2=>'jpeg', 3 =>'png', 4=>'wbmp', 5=>'webp', 6=>'xbm', 7=>'xpm');		
		$imageInfo = getimagesize($src);
		$this->srcWidth = $imageInfo[0];
		$this->srcHeight = $imageInfo[1];
		$method = 'imagecreatefrom'.$srcMethod[$imageInfo[2]];
		$this->srcImage = $method($src);
		return $this;
	}
	
	/**
	 * 设置缩略图的长度和高度
	 * @param int $width
	 * @param int $height
	 */
	public function setDistSize($width, $height)
	{
		$this->distWidth = $width;
		$this->disHeight = $height;
		return $this;
	}
	
	/**
	 * 按照百分比进行缩放
	 * @param unknown $percentage
	 */
	public function setDistSizeByPercentage($percentage)
	{
		$this->distWidth = $this->srcWidth * $percentage;
		$this->disHeight = $this->srcHeight * $percentage;
		return $this;
	}
	
	/**
	 * 创建缩略图
	 */
	public function create($dist, $type='jpeg')
	{
		// 创建新的画布
		$distImage = imagecreatetruecolor($this->distWidth, $this->disHeight);
		
		// 完整拷贝
		imagecopyresampled(
				$distImage, $this->srcImage,
				0, 0, 0, 0,
				$this->distWidth, $this->disHeight, $this->srcWidth, $this->srcHeight
		);

		// 生成缩略图
		$method = "image{$type}";
		$method($distImage, $dist);
		
		imagedestroy($distImage);
		return $this;
	}
	
	/**
	 * 销毁原图资源
	 */
	public function destroy()
	{
		imagedestroy($this->srcImage);
	}
}
