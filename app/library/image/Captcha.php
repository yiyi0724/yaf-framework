<?php

/**
 * 验证码类
 * @author enychen
 */
namespace Image;

class Captcha {

	/**
	 * 图片长度
	 * @var int
	 */
	protected $width = 100;

	/**
	 * 图片宽度
	 * @var int
	 */
	protected $height = 40;

	/**
	 * 字体
	 * @var string
	 */
	protected $font = 'Elephant.ttf';
	
	/**
	 * 验证码内容
	 * @var string
	 */
	protected $code = NULL;

	/**
	 * 画布
	 * @var resource
	 */
	protected $canvas = NULL;

	/**
	 * 画布背景颜色
	 * @var array
	 */
	protected $canvasBgColor = array(255, 255, 255);	

	/**
	 * 设置验证码长度
	 * @param int $width 验证码长度
	 * @return Captcha $this 返回当前对象进行连贯操作
	 */
	public function setWidth($width) {
		$this->width = $width;
		return $this;
	}

	/**
	 * 获取验证码长度
	 * @return int
	 */
	public function getWidth() {
		return $this->width;
	}

	/**
	 * 设置验证码宽度
	 * @param int $height 验证码宽度
	 * @return Captcha $this 返回当前对象进行连贯操作
	 */
	public function setHeight($height) {
		$this->height = $height;
		return $this;
	}

	/**
	 * 获取验证码宽度
	 * @return int
	 */
	public function getHeight() {
		return $this->height;
	}

	/**
	 * 设置验证码内容
	 * @return Captcha $this 返回当前对象进行连贯操作
	 */
	protected function setCode() {
		if(mt_rand(0, 1)) {
			$this->code = substr(str_shuffle('abcdefghijkmnpqrstuvwxyzABCDEFJHIJKLMNPQRSTUVWXYZ'), 4, 4);
		} else {
			$this->code = (string)mt_rand(1000, 9999);
		}
		return $this;
	}

	/**
	 * 获取验证码内容
	 * @return string
	 */
	public function getCode() {
		return $this->code;
	}

	/**
	 * 设置画布
	 * @return Captcha $this 返回当前对象进行连贯操作
	 */
	protected function setCanvas() {
		$this->canvas = imagecreatetruecolor($this->getWidth(), $this->getHeight());		
		$bgColor = $this->getCanvasBgColor();
		$bgColor = imagecolorallocate($this->canvas, $bgColor[0], $bgColor[1], $bgColor[2]);
		imagefilledrectangle($this->canvas, 0, 0, $this->getWidth(), $this->getHeight(), $bgColor);
		imagecolortransparent($this->canvas, $bgColor);
		return $this;
	}

	/**
	 * 获取画布
	 * @return resource
	 */
	protected function getCanvas() {
		return $this->canvas;
	}

	/**
	 * 设置画布背景颜色
	 * @param int $red 红色
	 * @param int $yellow 黄色
	 * @param int $blue 蓝色
	 * @return Captcha $this 返回当前对象进行连贯操作 
	 */
	public function setCanvasBgColor($red, $yellow, $blue) {
		$this->canvasBgColor[0] = $red;
		$this->canvasBgColor[1] = $yellow;
		$this->canvasBgColor[2] = $blue;
		return $this;
	}

	/**
	 * 获取画布背景颜色
	 * @return array
	 */
	public function getCanvasBgColor() {
		return $this->canvasBgColor;
	}

	/**
	 * 设置字体（字体文件必须放在\library\image\Fonts目录下）
	 * @param string $font 只需要字体的名称，无需完整路径
	 * @return Captcha $this 返回当前对象进行连贯操作
	 */
	public function setFont($font) {
		$this->font = $font;
		return $this;
	}

	/**
	 * 获取字体
	 * @return string
	 */
	public function getFont() {
		return sprintf('%s%sFonts%s%s', __DIR__, DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR, $this->font);
	}

	/**
	 * 获取字体大小
	 * @return int
	 */
	protected function getFontSize($size = 4) {
		return $this->getHeight() / $size * 1.8;
	}

	/**
	 * 生成并输出验证码图片
	 * @return void
	 */
	public function show() {
		// 初始化透明画布		
		$canvas = $this->setCanvas()->getCanvas();
		// 获取验证码
		$code = $this->setCode()->getCode();
		// 获取字体信息
		$font =$this->getFont();
		$fontSize = $this->getFontSize();
		
		// 填充文字
		$spacing = floor($this->getWidth() / $this->getHeight());
		for($i = 0, $len = strlen($code); $i < $len; $i++) {
			$angle = mt_rand(-20, 20);
			$x = ($i+0.3) * $this->getWidth()/4.5;
			$y = ($this->getHeight() / 1.3) + mt_rand(2, 4);
			$color = imagecolorallocate($canvas, mt_rand(0, 200), mt_rand(0, 200), mt_rand(0, 200));
			imagettftext($canvas, $fontSize, $angle, $x, $y, $color, $font, $code[$i]);
		}

 		// 画星星
 		$length = floor(($this->getWidth() + $this->getHeight()) / 3);
		for($i = 0; $i <$length; $i++) {
			$color = imagecolorallocate($canvas, mt_rand(0, 200), mt_rand(0, 200), mt_rand(0, 200));
			imagettftext($canvas, 9, 0, mt_rand(5, $this->getWidth()), mt_rand(10, $this->getHeight()), $color, $font, '.');
		}

		// 输出
		header('Content-type: image/png;charset=UTF-8');
		imagepng($canvas);
		imagedestroy($canvas);
	}
}