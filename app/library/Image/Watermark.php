<?php
/**
 * 水印图
 * @author enychen
 * @version 1.0
 */
namespace Image;

class Watermark {	
	/**
	 * 水印文字
	 * @var string
	 */
	private $waterText;
	
	/**
	 * 水印图
	 * @var string
	 */
	private $waterImage;
	
	/**
	 * 源图信息
	 * @var array
	 */
	private $srcImage;
	
	/**
	 * 保存图
	 * @var string
	 */
	private $distImage;

	/**
	 * 水印位置(1,2,3,4,5,6,7,8,9)
	 * @var string
	 */
	private $position = array('location'=>9, 'margin'=>5);

	/**
	 * 错误代码
	 * @var int
	 */
	private $errorCode = 0;
	
	/**
	 * 错误信息
	 * @var int
	 */
	private $errorMsg;
	
	/**
	 * 错误提示
	 * @var int
	 */
	private $errorNotify = array(
		0 => '未知错误',
		1 => '源图不存在',
		2 => '未设置源图信息',
		3 => '水印图片不存在',
		4 => '未设置水印信息',
		5 => '未设置图片保存路径',
		6 => '保存目录无操作权限',
	);
	
	/**
	 * 设置水印文字
	 * @param string $text 水印图片名称
	 * @param array $color 字体颜色（红，黄，蓝）
	 * @param int $fontsize 字体大小
	 * @return \Image\Watermark 可以进行连续操作
	 */
	public function setWaterText($text, $color, $fontsize) {
		$this->waterText['text'] = $text;
		$this->waterText['color'] = $color;
		$this->waterText['fontsize'] = $fontsize;
		
		return $this;
	}
	
	/**
	 * 设置水印图
	 * @param string $waterFilename 水印图片名称
	 * @param int $opacity 透明度，如果设置为0，表示水印图自带透明效果
	 * @param float $ratio 水印图缩略[0-1], 默认为图片的0.5倍
	 * @return \Image\Watermark 可以进行连续操作
	 */
	public function setWaterFilename($waterFilename, $opacity = 0, $ratio = 0.5) {
		// 图片是否存在
		if(!is_file($waterFilename)) {
			$this->setOption('errorCode', 3);
			return $this;
		}
		
		// 分析图片信息
		$imageInfo = getimagesize($waterFilename);
		$this->waterImage['width'] = $imageInfo[0];
		$this->waterImage['height'] = $imageInfo[1];
		$this->waterImage['filename'] = $waterFilename;
		$this->waterImage['opacity'] = $opacity;
		$this->waterImage['ratio'] = $ratio;
		
		return $this;
	}
	
	/**
	 * 设置源图
	 * @param string $waterFilename 原始图片名称
	 * @return \Image\Watermark 可以进行连续操作
	 */
	public function setSrcFilename($srcFilename) {
		// 图片是否存在
		if(!is_file($srcFilename)) {
			$this->setOption('errorCode', 1);
			return $this;
		}
		
		// 分析图片信息
		$imageInfo = getimagesize($srcFilename);
		$this->srcImage['width'] = $imageInfo[0];
		$this->srcImage['height'] = $imageInfo[1];
		$this->srcImage['filename'] = $srcFilename;
		
		return $this;
	}
	
	/**
	 * 设置目标图片
	 * @param string $waterFilename 原始图片名称
	 * @return \Image\Watermark 可以进行连续操作
	 */
	public function setDistFilename($distFilename) {
		$this->distImage['path'] = trim(dirname($distFilename), '/') . '/';
		$this->distImage['filename'] = $distFilename;
		$ext = trim(strrchr(basename($distFilename), '.'), '.');
		$this->distImage['ext'] = $ext == 'jpg' ? 'jpeg' : $ext;

		return $this;
	}
	
	/**
	 * 设置水印位置
	 * @param string $position 水印位置（1,2,3,4,5,6,7,8,9）
	 * @param int $padding 水印的边距
	 * @return \Image\Watermark
	 */
	public function setPosition($position, $margin = 0) {
		$this->position['location'] = $position;
		$this->padding['margin'] = $margin;
		return $this;
	}
	
	/**
	 * 获取错误信息
	 * @return string
	 */
	public function getErrorMsg() {
		return $this->errorMsg;
	}
	
	/**
	 * 加水印
	 * @return bool 创建成功返回TRUE
	 */
	public function mark() {
		// 是否有设置必备选项
		if(!$this->checkSetOptions()) {
			$this->setOption('errorMsg', $this->getError());
			return FALSE;
		}
		
		// 目录是否可写
		if(!$this->isWritable()) {
			$this->setOption('errorMsg', $this->getError());
			return FALSE;
		}
	
		// 生成水印图
		if(!$this->markImage()) {
			$this->setOption('errorMsg', $this->getError());
			return FALSE;
		}
		
		return TRUE;
	}
	
	/**
	 * 获取错误信息
	 * @return number
	 */
	private function getError() {
		return isset($this->errorNotify[$this->errorCode]) ? $this->errorNotify[$this->errorCode] : $this->errorNotify[0];
	}
	
	/**
	 * 设置单个属性值
	 * @param string $key 属性名
	 * @param mixed $val 属性值
	 * @return void
	 */
	private function setOption($key, $val) {
		$this->$key = $val;
	}
	
	/**
	 * 目录是否可以写入(不存在尝试创建)
	 * @return boolean
	 */
	private function isWritable() {
		if(!is_dir($this->distImage['path']) || !is_writable($this->distImage['path'])) {
			if(!@mkdir($this->distImage['path'], 0755, TRUE)) {
				$this->setOption('errorCode', 6);
				return FALSE;
			}
		}
		return TRUE;
	}
	
	/**
	 * 生成缩略图
	 */
	private function markImage() {
		// 原图资源
		$srcImage = imagecreatefromstring(file_get_contents($this->srcImage['filename']));
		// 水印图资源
		$waterImage = imagecreatefromstring(file_get_contents($this->waterImage['filename']));
		
		// 水印相对原图缩放
/* 		$targetWidth = $this->srcImage['width']*$this->waterImage['ratio'];
		
		$distImage = imagecreatetruecolor($targetWidth, $targetHeight);
		imagesavealpha($srcImage, TRUE);		// 不要丢了$srcImage图像的透明色;
		imagealphablending($distImage, FALSE);	// 不合并颜色,直接用$distImage图像颜色替换,包括透明色;
		imagesavealpha($distImage, TRUE);		// 不要丢了$distImage图像的透明色;
		imagecopyresampled($distImage, $waterImage, 0, 0, 0, 0,
			$distWidth, $distHeight, $this->waterImage['width'], $this->waterImage['height']);
		$waterImage = $distImage;
		$this->waterImage['width'] = $distWidth;
		$this->waterImage['height'] = $distHeight;
		if($this->waterImage['ratio'] < 1) {
		} */
		
		// 计算位置
		list($x, $y) = $this->getOffset();
		
		// 图片透明度
		if($this->waterImage['opacity']) {
			//将水印图片复制到目标图片上，最后个参数50是设置透明度，这里实现半透明效果
			imagecopymerge($srcImage, $waterImage, $x, $y, 0, 0, 
				$this->waterImage['width'], $this->waterImage['height'], $this->waterImage['opacity']);
		} else {
			//如果水印图片本身带透明色，则使用imagecopy方法
			imagecopy($srcImage, $waterImage, $x, $y, 0, 0, $this->waterImage['width'], $this->waterImage['height']);
		}
		
		// 销毁原图资源
		imagedestroy($waterImage);
		
		// 保存图片
		call_user_func("image{$this->distImage['ext']}", $srcImage, $this->distImage['filename']);
		imagedestroy($srcImage);
		
		return TRUE;
	}
	
	/**
	 * 计算坐标点
	 * @return array x坐标,y坐标
	 */
	private function getOffset() {
		switch($this->position['location']) {
			case 1:
				// 左上
				$x = $this->position['margin'];
				$y = $this->position['margin'];
				break;
			case 2:
				// 中上
				$x = ($this->srcImage['width'] - $this->waterImage['width'])/2;
				$y = $this->position['margin'];
				break;
			case 3:
				// 右上
				$x = $this->srcImage['width'] - $this->waterImage['width'] - $this->position['margin'];
				$y = $this->position['margin'];
				break;
			case 4:
				// 左中
				$x = $this->position['margin'];
				$y = ($this->srcImage['height'] - $this->waterImage['height'])/2;
				break;
			case 5:
				// 正中
				$x = ($this->srcImage['width'] - $this->waterImage['width'])/2;
				$y = ($this->srcImage['height'] - $this->waterImage['height'])/2;
				break;
			case 6:
				// 右中
				$x = $this->srcImage['width']  - $this->waterImage['width'] - $this->position['margin'];
				$y = ($this->srcImage['height'] - $this->waterImage['height'])/2;
				break;
			case 7:
				// 左下
				$x = $this->position['margin'];
				$y = $this->srcImage['height'] - $this->waterImage['height'] - $this->position['margin'];
				break;
			case 8:
				// 中下
				$x = ($this->srcImage['width'] - $this->waterImage['width'])/2;
				$y = $this->srcImage['height'] - $this->waterImage['height'] - $this->position['margin'];
				break;
			default:
				// 右下
				$x = $this->srcImage['width']  - $this->waterImage['width'] - $this->position['margin'];
				$y = $this->srcImage['height'] - $this->waterImage['height'] - $this->position['margin'];
				break;
		}
		
		return array($x, $y);
	}
	
	/**
	 * 检查必须设置的选项是否有进行设置
	 * @return boolean
	 */
	private function checkSetOptions() {
		// setOption的时候存在错误
		if($this->errorCode) {
			return FALSE;
		}
		
		// 是否有设置源图
		if(!$this->srcImage) {
			$this->setOption('errorCode', 2);
			return FALSE;
		}
		
		// 是否有设置水印信息
		if(!$this->waterImage && !$this->waterText) {
			$this->setOption('errorCode', 4);
			return FALSE;
		}
		
		// 是否有设置保存信息
		if(!$this->distImage) {
			$this->setOption('errorCode', 5);
			return FALSE;
		}
		
		return TRUE;
	}
}