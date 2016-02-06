<?php

namespace Image;

class Captcha
{

	protected $randomCode = 'abcdefghijklmnpqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ123456789';

	/**
	 * 字体库
	 * @var array
	 */
	protected $fonts = array('brokenrecords_33.ttf', 'brokenrecords_45.ttf', 'telefono.ttf');

	/**
	 * 构造函数
	 * @param int $width 长度
	 * @param int $heigh 宽度
	 * @param int $length 字符个数
	 * @param string $font 字体名称
	 */
	public function __construct($width = 100, $height = 40, $length = 4, $font = NULL)
	{
		$this->width = $width;
		$this->height = $height;
		$this->image = imagecreatetruecolor($width, $height);
		$this->code = $this->randomCode($length);
		$this->font = __DIR__.'/Fonts/'.$this->getFont();
	}

	/**
	 * 画图
	 */
	public function draw()
	{
		// 画布颜色
		$color = imagecolorallocate($this->image, 255, 255, 255);
		
		// 填充颜色
		imagefilledrectangle($this->image, 0, $this->height, $this->width, 0, $color);

		// 填充文字
		for($i=0, $len=strlen($this->code); $i<$len; $i++)
		{
			$color = imagecolorallocate($this->image, mt_rand(0, 200), mt_rand(0, 200), mt_rand(0, 200));
			imagettftext(
					$this->image,
					$this->height * 0.5,
					mt_rand(-30, 30),
					10+$this->width / 5 * $i,
					$this->height / 1.4,
					$color,
					$this->font,
					$this->code[$i]
			);
		}
	}
	
	/**
	 * 获取生成的验证码
	 */
	public function getCode()
	{
		return $this->code();
	}
	
	public function output()
	{
		// 输出
		header('Content-type: image/png');
		imagepng($this->image);
		// 销毁
		imagedestroy($this->image);
	}

	/**
	 * 获取随机码
	 * @return string
	 */
	protected function randomCode($length)
	{
		return substr(str_shuffle($this->randomCode), 0, $length);
	}

	/**
	 * 选择何种字体
	 * @param string 字体路径 $font
	 */
	protected function getFont()
	{
		return $this->fonts[mt_rand(0, count($this->fonts) - 1)];
	}
}