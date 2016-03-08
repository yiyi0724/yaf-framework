<?php
/**
 * 输出验证码
 * @author chenxb
 */
namespace Image;

class Captcha
{
	/**
	 * 构造函数
	 * @param int $width 图片长度
	 * @param int $heigh 图片宽度
	 * @param int $length 字符个数
	 */
	public function __construct($width = 100, $height = 40, $length = 4)
	{
		// 保存初始化值
		$this->width = $width;
		$this->height = $height;
		$this->length = $length;
		$this->image = imagecreatetruecolor($width, $height);
		$this->font = __DIR__.'/Fonts/Elephant.ttf';
		$this->fontSize = $this->height / $this->length * 2.4;
		$this->code = NULL;
		
		// 生成随机码
		$this->getRandomCode();
	}
	
	/**
	 * 获取随机验证码
	 */
	protected function getRandomCode()
	{
		$charset = 'abcdefghijkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ123456789';		
		for($i=0; $i<$this->length; $i++)
		{
			$this->code .= $charset[mt_rand(0,56)];
		}
	}
		
	/**
	 * 生成星星
	 * @param int $star 星星个数
	 */
	public function createLine($star = 130)
	{
		for($i = 0; $i < $star; $i++)
		{
			$color = imagecolorallocate($this->image, mt_rand(200, 255), mt_rand(200, 255), mt_rand(200, 255));
			imagestring($this->image, mt_rand(1, 5), mt_rand(0, $this->width), mt_rand(0, $this->height), '.', $color);
		}
	}
	
	/**
	 * 设置字体大小
	 * @param int $fontSize 字体大小值
	 */
	public function setFontSize($fontSize)
	{
		$this->fontSize = $fontSize;
	}

	/**
	 * 生成文字
	 */
	public function setText()
	{
		// 画布颜色
		$color = imagecolorallocate($this->image, 200, 200, 200);
		
		// 填充颜色
		imagefilledrectangle($this->image, 0, $this->height, $this->width, 0, $color);

		// 透明颜色
		imagecolortransparent($this->image, $color);
		
		// 填充文字
		$spacing = $this->width / $this->length;
		$y = $this->height / 1.3;
		for($i=0, $len=strlen($this->code); $i<$len; $i++)
		{
			$angle = mt_rand(-30, 30);
			$x = $spacing*$i+mt_rand(1,3);
			$color = imagecolorallocate($this->image, mt_rand(0, 200), mt_rand(0, 200), mt_rand(0, 200));
			imagettftext($this->image, $this->fontSize, $angle, $x, $y, $color, $this->font, $this->code[$i]);
		}
	}
	
	/**
	 * 获取生成的验证码
	 * @return string
	 */
	public function getCode()
	{
		return $this->code;
	}
	
	/**
	 * 输出图片并且结束php程序
	 */
	public function output()
	{
		// 输出
		header('Content-type: image/png');
		imagepng($this->image);
		// 销毁
		imagedestroy($this->image);
	}
}