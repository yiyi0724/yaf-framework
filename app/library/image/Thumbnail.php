<?php

/**
 * 缩略裁剪图
 * @author enychen
 * @version 1.0
 */
namespace Image;

class Thumbnail {

	/**
	 * 源图
	 * @var array
	 */
	private $srcImage = NULL;

	/**
	 * 保存图
	 * @var array
	 */
	private $distImage = NULL;

	/**
	 * 错误信息列表
	 * @var array
	 */
	private $errorNotify = array(
		0=>'未知错误',
		1=>'源图不存在',
		2=>'源图格式不合法',
		3=>'保存目录无操作权限',
		4=>'图片格式不支持',
	);

	/**
	 * 设置源图
	 * @param string $srcFilename 原始图片名称
	 * @return void
	 */
	private function setSrcFilename($srcFilename) {
		// 图片是否存在
		if(!is_file($srcFilename)) {
			$this->throwError(1);
		}

		// 分析图片信息
		$imageInfo = @getimagesize($srcFilename);
		if(!$imageInfo) {
			$this->throwError(2);
		}

		// 分析图片格式
		$mineType = explode('/', $imageInfo['mime']);

		// 保存信息
		$this->srcImage['ext'] = $mineType[1];
		$this->srcImage['width'] = $imageInfo[0];
		$this->srcImage['height'] = $imageInfo[1];
		$this->srcImage['resource'] = $this->openFile($srcFilename);
	}

	/**
	 * 设置目标图片
	 * @param string $distFilename 目标图片名称
	 * @return void
	 */
	private function setDistFilename($distFilename) {
		$distInfo = pathinfo($distFilename);
		$path = $distInfo['dirname'] . DIRECTORY_SEPARATOR;
		$ext = str_replace('jpg', 'jpeg', $distInfo['extension']);

		// 保存目录判断是否存在
		if(!is_dir($path) || !is_writable($path)) {
			if(!@mkdir($path, 0755, TRUE)) {
				$this->throwError(3);
			}
		}

		// 保存信息
		$this->distImage['ext'] = $ext;
		$this->distImage['path'] = $path;
		$this->distImage['filename'] = $distFilename;
	}

	/**
	 * 载入图片资源
	 * @param string $filename 图片地址
	 * @return resource
	 */
	private function openFile($filename) {
		return imagecreatefromstring(file_get_contents($filename));
	}

	/**
	 * 抛出异常信息
	 * @throws \Exception
	 * @return void
	 */
	private function throwError($errorCode = 0) {
		$error = isset($this->errorNotify[$errorCode]) ? $this->errorNotify[$errorCode] : $this->errorNotify[0];
		throw new \Exception($error, $errorCode);
	}

	/**
	 * 图片输出或者进行保存
	 * @param int $witdh 缩略长度
	 * @param int $height 缩略高度
	 * @param int $x 裁剪x轴坐标
	 * @param int $y 裁剪y轴坐标
	 * @return void
	 */
	private function saveOrOutput($width, $height, $startX, $startY, $endX, $endY) {
		$ext = $this->distImage ? $this->distImage['ext'] : $this->srcImage['ext'];
		if(!function_exists("image{$ext}")) {
			$this->throwError(4);
		}
		
		$distResource = imagecreatetruecolor($width, $height);
		imagesavealpha($this->srcImage['resource'], TRUE);
		imagealphablending($distResource, FALSE);
		imagesavealpha($distResource, TRUE);

		$endX = $endX ?  : $this->srcImage['width'];
		$endY = $endY ?  : $this->srcImage['height'];

		imagecopyresampled($distResource, $this->srcImage['resource'], 0, 0,
			$startX, $startY, $width, $height, $endX, $endY);

		if($this->distImage) {
			call_user_func("image{$ext}", $distResource, $this->distImage['filename']);
		} else {
			header("Content-Type: image/{$ext};charset=UTF-8");
			call_user_func("image{$ext}", $distResource);
		}

		imagedestroy($this->srcImage['resource']);
		imagedestroy($distResource);
	}

	/**
	 * 通过设置width和height进行图像缩略
	 * @throws \Exception
	 * @param string $srcFilename 源图文件名
	 * @param string $distFilename 保存文件名，如果直接输出设置成NULL
	 * @param int $witdh 缩略长度
	 * @param int $height 缩略高度
	 * @param int $startX 裁剪x轴起始坐标
	 * @param int $startY 裁剪y轴起始坐标
	 * @param int $endX 裁剪x轴终点坐标
	 * @param int $endY 裁剪y轴终点坐标
	 * @return void
	 */
	public function byWidthHeight($srcFilename, $distFilename, $width, $height, $startX = 0, $startY = 0, $endX = NULL, $endY = NULL) {
		$this->setSrcFilename($srcFilename);
		$distFilename and $this->setDistFilename($distFilename);
		
		$this->saveOrOutput($width, $height, $startX, $startY, $endX, $endY);
	}

	/**
	 * 通过width或height的最大值进行图像等比缩略
	 * @throws \Exception
	 * @param string $srcFilename 源图文件名
	 * @param string $distFilename 保存文件名，如果直接输出设置成NULL
	 * @param int $size 缩略到的尺寸
	 * @param int $startX 裁剪x轴起始坐标
	 * @param int $startY 裁剪y轴起始坐标
	 * @param int $endX 裁剪x轴终点坐标
	 * @param int $endY 裁剪y轴终点坐标
	 * @return void
	 */
	public function byMaxEdge($srcFilename, $distFilename, $size, $startX = 0, $startY = 0, $endX = NULL, $endY = NULL) {
		$this->setSrcFilename($srcFilename);
		$distFilename and $this->setDistFilename($distFilename);

		$max = max($this->srcImage['width'], $this->srcImage['height']);
		$proportion = $size / $max;
		$width = floor($proportion * $this->srcImage['width']);
		$height = floor($proportion * $this->srcImage['height']);

		$this->saveOrOutput($width, $height, $startX, $startY, $endX, $endY);
	}
}