<?php

/**
 * 输出验证码
 * @author chenxb
 */
namespace Image;

class Captcha {

	/**
	 * 构造函数
	 * @param int $width 图片长度
	 * @param int $heigh 图片宽度
	 * @param int $length 字符个数
	 * @return void
	 */
	public function __construct($width = 100, $height = 40, $length = 4) {
		// 保存初始化值
		$this->width = $width;
		$this->height = $height;
		$this->length = $length;
		$this->image = imagecreatetruecolor($width, $height);
		$this->font = __DIR__ . '/Fonts/Elephant.ttf';
		$this->fontSize = $height / $length * 1.9;
		$this->backgroundColor = imagecolorallocate($this->image, 255, 255, 255);
		$this->code = $this->star = $this->line = NULL;
		
		// 生成随机码
		$this->getRandomCode();
	}

	/**
	 * 获取随机验证码
	 * @return void
	 */
	protected function getRandomCode() {
		$charset = 'abcdefghijkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ123456789';
		for($i = 0; $i < $this->length; $i++) {
			$this->code .= $charset[mt_rand(0, 56)];
		}
	}

	/**
	 * 生成干扰星星
	 * @param int $star 星星个数
	 * @return void
	 */
	public function setStar($star = 100) {
		$this->star = $star;		
	}
	
	/**
	 * 生成干扰线
	 * @param int $star 星星个数
	 * @return void
	 */
	public function setLine($line = 1) {
		$this->line = $line;
	}
	
	/**
	 * 设置字体
	 * @param string $font 字体名称，包含后缀
	 * @return void
	 */
	public function setFont($font) {
		$this->font = __DIR__ . "/Fonts/{$font}";
	}
	
	/**
	 * 更改字体大小
	 * @param int $fontSize 字体大小值
	 * @return void
	 */
	public function setFontSize($fontSize) {
		$this->fontSize = $fontSize;
	}

	/**
	 * 获取生成的验证码
	 * @return string
	 */
	public function getCode() {
		return $this->code;
	}

	/**
	 * 设置背景颜色
	 */
	public function setBackgroundColor($red, $green, $blue) {
		$this->backgroundColor = imagecolorallocate($this->image, $red, $green, $blue);
	}

	/**
	 * 生成并输出验证码图片
	 * @return void
	 */
	public function show() {		
		// 填充颜色 && 透明颜色
		imagefilledrectangle($this->image, 0, $this->height, $this->width, 0, $this->backgroundColor);
		imagecolortransparent($this->image, $this->backgroundColor);

		// 画星星
		if($this->star) {
			for($i = 0; $i < $this->star; $i++) {
				$color = imagecolorallocate($this->image, mt_rand(0, 200), mt_rand(0, 200), mt_rand(0, 200));
				$result = imagestring($this->image, mt_rand(0, 5), mt_rand(0, $this->width), mt_rand(0, $this->height), '.', $color);
			}
		}

		// 画干扰线
		if($this->line) {
			for($i = 0; $i < $this->line; $i++) {
				$color = imagecolorallocate($this->image, mt_rand(0, 200), mt_rand(0, 200), mt_rand(0, 200));
				$result = imageline($this->image, mt_rand(0, $this->width-5), mt_rand($this->height*0.2, $this->height*0.8), 
					mt_rand($this->width-5, $this->width), mt_rand(0, $this->height), $color);
			}
		}		

		// 填充文字
		$spacing = floor($this->width / $this->length);
		$y = $this->height / 1.3;
		for($i = 0, $len = strlen($this->code); $i < $len; $i++) {
			$angle = mt_rand(-30, 30);
			$x = $spacing * $i + mt_rand(1, 3)+1;
			$color = imagecolorallocate($this->image, mt_rand(0, 200), mt_rand(0, 200), mt_rand(0, 200));
			imagettftext($this->image, $this->fontSize, $angle, $x, $y, $color, $this->font, $this->code[$i]);
		}
		
		// 输出
		header('Content-type: image/png;charset=UTF-8');
		imagepng($this->image);
		// 销毁
		imagedestroy($this->image);
	}
}