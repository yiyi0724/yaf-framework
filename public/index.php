<?php

// 站点目录
define('SITE_PATH', dirname(__DIR__) . DIRECTORY_SEPARATOR);
// 配置目录
define('CONF_PATH', SITE_PATH . 'conf' . DIRECTORY_SEPARATOR);
// 数据目录
define('DATA_PATH', SITE_PATH . 'data' . DIRECTORY_SEPARATOR);
// 项目目录
define('APPLICATION_PATH', SITE_PATH . 'app' . DIRECTORY_SEPARATOR);

// 启动框架
$app = new \Yaf\Application(CONF_PATH . 'app.ini');
$app->bootstrap()->run();


<?php
/**
 * 水印图
* @author enychen
* @version 1.0
*/
namespace Image;

class Watermark {

	/**
	 * 图片保存的目录
	 * @var string
	 */
	private $distPath = './watermark/';

	/**
	 * 水印文字
	 * @var string
	 */
	private $waterText;

	/**
	 * 水印图
	 * @var string
	 */
	private $waterImageInfo = array('filename'=>NULL, 'width'=>0, 'height'=>0);

	/**
	 * 源图信息
	 * @var array
	*/
	private $srcInfo = array('filename'=>NULL, 'width'=>0, 'height'=>0);

	/**
	 * 保存图
	 * @var string
	*/
	private $distInfo = array('filename'=>NULL, 'width'=>0, 'height'=>0);

	/**
	 * 水印位置(1,2,3,4,5,6,7,8,9)
	 * @var string
	*/
	private $position;

	/**
	 * 水印间距
	 * @var string
	 */
	private $padding = 0;

	/**
	 * 缩略图片宽度
	 * @var int
	 */
	private $distwidth = 0;

	/**
	 * 原始图片高度
	 * @var int
	 */
	private $distheight = 0;

	/**
	 * 原始图片格式
	 * @var int
	 */
	private $srcType;

	/**
	 * 缩略图片格式
	 * @var int
	 */
	private $disttype;

	/**
	 * 错误代码
	 * @var int
	 */
	private $errorCode;

	/**
	 * 错误信息
	 * @var int
	 */
	private $errorMsg;

	public function setWaterText($text, $color, $fontsize) {

	}

	/**
	 * 设置水印图
	 * @param string $waterFilename 水印图片名称
	 * @return \Image\Watermark 可以进行连续操作
	 */
	public function setWaterFilename($waterFilename) {
		// 图片是否存在
		if(!is_file($waterFilename)) {
			$this->setOption('errorCode', -2);
			return $this;
		}

		// 分析图片信息
		$imageInfo = getimagesize($waterFilename);
		$this->waterImageInfo['width'] = $imageInfo[0];
		$this->waterImageInfo['height'] = $imageInfo[1];
		$this->waterImageInfo['filename'] = $waterFilename;

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
			$this->setOption('errorCode', -1);
			return $this;
		}

		// 分析图片信息
		$imageInfo = getimagesize($srcFilename);
		$this->srcInfo['width'] = $imageInfo[0];
		$this->srcInfo['height'] = $imageInfo[1];
		$this->srcInfo['filename'] = $srcFilename;

		return $this;
	}

	/**
	 * 设置目标图片
	 * @param string $waterFilename 原始图片名称
	 * @return \Image\Watermark 可以进行连续操作
	 */
	public function setDistFilename($distFilename) {
		// 图片是否存在
		if(!is_file($distFilename)) {
			$this->setOption('errorCode', -3);
			return $this;
		}

		// 分析图片信息
		$imageInfo = getimagesize($srcFilename);
		$this->srcInfo['width'] = $imageInfo[0];
		$this->srcInfo['height'] = $imageInfo[1];
		$this->srcInfo['filename'] = $srcFilename;

		return $this;
	}

	/**
	 * 设置水印位置
	 * @param string $position 水印位置（1,2,3,4,5,6,7,8,9）
	 * @param int $padding 水印的边距
	 * @return \Image\Watermark
	 */
	public function setPosition($position, $padding = 0) {
		$this->position = $position;
		$this->padding = $padding;
		return $this;
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
	 * 加水印
	 * @return bool 创建成功返回TRUE
	 */
	public function mark() {
		// 检查文件是否存在
		if(!$this->isWritable()) {
			$this->setOption('errorMsg', $this->getError());
			return FALSE;
		}

		// 设置图片信息
		if(!$this->setSrcInfo()) {
			$this->setOption('errorMsg', $this->getError());
			return FALSE;
		}

		// 设置目标图片信息
		if(!$this->setDistInfo()) {
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

	/**
	 * 获取完整目标缩略图名称
	 * @return string
	 */
	public function getDistFileName() {
		return $this->distfilename;
	}

	/**
	 * 获取错误信息
	 * @return string
	 */
	public function getErrorMsg() {
		return $this->errorMsg;
	}

	/**
	 * 生成缩略图
	 */
	private function copy() {
		// 载入旧的画布
		$srcImage = call_user_func("imagecreatefrom{$this->srcType}", $this->srcfilename);

		// 创建新的画布
		$distImage = imagecreatetruecolor($this->distwidth, $this->distheight);
		switch(TRUE) {
			case $this->disttype == 'png':
				// 保存格式为png
				imagesavealpha($srcImage, TRUE);		// 不要丢了$srcImage图像的透明色;
				imagealphablending($distImage, FALSE);	// 不合并颜色,直接用$distImage图像颜色替换,包括透明色;
				imagesavealpha($distImage, TRUE);		// 不要丢了$distImage图像的透明色;
				break;
			case $this->srcType == 'png' && $this->disttype != 'png':
				// png转其他格式
				$color = imagecolorallocate($distImage, 255, 255, 255);
				imagefill($distImage, 0, 0, $color);
				break;
		}

		// 完整拷贝
		imagecopyresampled($distImage, $srcImage, 0, 0, $this->srcx, $this->srcy, $this->distwidth, $this->distheight, $this->srcwidth, $this->srcheight);

		// 销毁原图资源
		imagedestroy($srcImage);
		// 生成缩略图
		call_user_func("image{$this->disttype}", $distImage, $this->distfilename);
		imagedestroy($distImage);

		return TRUE;
	}

	/**
	 * 根据错误代码转成错误信息
	 * @return string
	 */
	private function getError() {
		switch($this->errorCode) {
			case -1:
				$str = '源图文件不存在';
				break;
			case -2:
				$str = '水印图片不存在';
				break;
			case -3:
				$str = '未设置水印保存路径';
				break;
			case -2:
				$str = '未设置保存目录';
				break;
			case -3:
				$str = '保存目录不存在或无操作权限';
				break;
			case -4:
				$str = '图片格式不支持转换';
				break;
			case -5:
				$str = '图片格式不支持保存';
				break;
			case -6:
				$str = '未设置保存文件名';
				break;
			case -7:
				$str = '缩略图尺寸大于原图尺寸';
				break;
			default:
				$str = '未知错误';
		}

		return $str;
	}

	/**
	 * 目录是否可以写入(不存在尝试创建)
	 * @return boolean
	 */
	private function isWritable() {
		// 源文件是否存在
		if(!is_file($this->srcFilename)) {
			$this->setOption('errorCode', -1);
			return FALSE;
		}

		// 存储目录是否存在
		if(empty($this->path)) {
			$this->setOption('errorCode', -2);
			return FALSE;
		}
		if(!is_dir($this->path) || !is_writable($this->path)) {
			if(!@mkdir($this->path, 0755, TRUE)) {
				$this->setOption('errorCode', -3);
				return FALSE;
			}
		}
		return TRUE;
	}

	/**
	 * 目标图片信息分析
	 * @return boolean 分析成功返回TRUE
	 */
	private function setDistInfo() {
		// 缩略图文件名
		if(empty($this->distfilename)) {
			$this->setOption('errorCode', -6);
			return FALSE;
		}

		// 格式检查
		$disttype = $this->disttype ? : $this->srcType;
		if(!in_array($disttype, $this->allowType)) {
			$this->setOption('errorCode', -5);
			return FALSE;
		}
		$this->setOption('disttype', $disttype);

		// 缩略图检查尺寸检查
		/* if($this->srcwidth < $this->distwidth || $this->srcheight < $this->distheight) {
		$this->setOption('errorCode', -7);
		return FALSE;
		} */



		// 缩略图宽高度
		switch(TRUE) {
			case $this->eometric:
				// 等比缩略到某一个尺寸
				if($this->srcwidth > $this->srcheight) {
					$this->setOption('distwidth', $this->eometric);
					$distheight = floor(($this->distwidth/$this->srcwidth) * $this->srcheight);
					$this->setOption('distheight', $distheight);
				} else {
					$this->setOption('distheight', $this->eometric);
					$distwidth = floor(($this->distheight/$this->srcheight) * $this->srcwidth);
					$this->setOption('distwidth', $distwidth);
				}
				break;
			case (!$this->distwidth && !$this->distheight):
				// 没有设置大小，生成百分百大小
				$this->setOption('distwidth', $this->srcwidth);
				$this->setOption('distheight', $this->srcheight);
				break;
		}

		// 文件名保存
		$disttype = ($disttype == 'jpeg') ? 'jpg' : $disttype;
		$distfilename = rtrim($this->path, '/') . '/' . $this->distfilename . '.' . $disttype;
		$this->setOption('distfilename', $distfilename);

		return TRUE;
	}
}

$watermark = new \Image\Watermark();
$watermark->setWaterFilename('enychen.png')->setSrcFilename('/home/eny/Downloads/yyq.png')->setPosition(-20, -20);
$watermark->mark();


/* $dst_path = 'dst.jpg';
 $src_path = 'src.jpg';
 //创建图片的实例
 $dst = imagecreatefromstring(file_get_contents($dst_path));
 $src = imagecreatefromstring(file_get_contents($src_path));
 //获取水印图片的宽高
 list($src_w, $src_h) = getimagesize($src_path);
 //将水印图片复制到目标图片上，最后个参数50是设置透明度，这里实现半透明效果
 imagecopymerge($dst, $src, 10, 10, 0, 0, $src_w, $src_h, 50);
 //如果水印图片本身带透明色，则使用imagecopy方法
 //imagecopy($dst, $src, 10, 10, 0, 0, $src_w, $src_h);
 //输出图片
 list($dst_w, $dst_h, $dst_type) = getimagesize($dst_path);
 switch ($dst_type) {
 case 1://GIF
 header('Content-Type: image/gif');
 imagegif($dst);
 break;
 case 2://JPG
 header('Content-Type: image/jpeg');
 imagejpeg($dst);
 break;
 case 3://PNG
 header('Content-Type: image/png');
 imagepng($dst);
 break;
 default:
 break;
 }
 imagedestroy($dst);
 imagedestroy($src); */