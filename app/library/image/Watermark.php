<?php

/**
 * 水印类
 * @author enychen
 */
namespace image;

class Watermark {

	/**
	 * 水印图
	 * @var array
	 */
	private $waterImage = NULL;

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
		2=>'未设置源图信息',
		3=>'水印图片不存在',
		4=>'未设置水印信息',
		5=>'保存目录无操作权限',
		6=>'水印图片格式不合法',
		7=>'源图格式不合法',
		8=>'图片格式不支持',
	);

	/**
	 * 设置水印图
	 * @param string $waterFilename 水印图片名称
	 * @return void
	 */
	private function setWaterFilename($waterFilename) {
		// 图片是否存在
		is_file($waterFilename) or $this->throwError(3);

		// 分析图片信息
		$imageInfo = getimagesize($waterFilename);
		if(!$imageInfo) {
			$this->throwError(6);
		}

		// 保存信息
		$this->waterImage['width'] = $imageInfo[0];
		$this->waterImage['height'] = $imageInfo[1];
		$this->waterImage['resource'] = $this->openFile($waterFilename);
	}

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
			$this->throwError(7);
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
				$this->throwError(5);
			}
		}

		// 保存信息
		$this->distImage['ext'] = $ext;
		$this->distImage['path'] = $path;
		$this->distImage['filename'] = $distFilename;		
	}

	/**
	 * 设置水印位置
	 * @param string $position 水印位置（1,2,3,4,5,6,7,8,9）
	 * @return void
	 */
	private function setPosition($position) {
		switch($position) {
			case 1:
				// 左上
				$x = 0;
				$y = 0;
				break;
			case 2:
				// 中上
				$x = ($this->srcImage['width'] - $this->waterImage['width']) / 2;
				$y = 0;
				break;
			case 3:
				// 右上
				$x = $this->srcImage['width'] - $this->waterImage['width'];
				$y = 0;
				break;
			case 4:
				// 左中
				$x = 0;
				$y = ($this->srcImage['height'] - $this->waterImage['height']) / 2;
				break;
			case 5:
				// 正中
				$x = ($this->srcImage['width'] - $this->waterImage['width']) / 2;
				$y = ($this->srcImage['height'] - $this->waterImage['height']) / 2;
				break;
			case 6:
				// 右中
				$x = $this->srcImage['width'] - $this->waterImage['width'];
				$y = ($this->srcImage['height'] - $this->waterImage['height']) / 2;
				break;
			case 7:
				// 左下
				$x = 0;
				$y = $this->srcImage['height'] - $this->waterImage['height'];
				break;
			case 8:
				// 中下
				$x = ($this->srcImage['width'] - $this->waterImage['width']) / 2;
				$y = $this->srcImage['height'] - $this->waterImage['height'];
				break;
			default:
				// 右下
				$x = $this->srcImage['width'] - $this->waterImage['width'];
				$y = $this->srcImage['height'] - $this->waterImage['height'];
				break;
		}
		
		$this->position = array('x'=>$x, 'y'=>$y);
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
	 * @return
	 */
	private function saveOrOutput() {
		// 最终格式
		$ext = $this->distImage ? $this->distImage['ext'] : $this->srcImage['ext'];
		if(!function_exists("image{$ext}")) {
			$this->throwError(8);
		}
		
		// 输出还是保存
		if($this->distImage) {
			call_user_func("image{$ext}", $this->srcImage['resource'], $this->distImage['filename']);
		} else {
			header("Content-Type: image/{$ext};charset=UTF-8");
			call_user_func("image{$ext}", $this->srcImage['resource']);
		}
	}

	/**
	 * 通过透明的png水印图
	 * @throws \Exception
	 * @param string $srcFilename 原始图片名称
	 * @param string $waterFilename 水印图片名称
	 * @param string $distFilename 目标图片名称,如果不设置则直接输出
	 * @param int $position 水印位置（1,2,3,4,5,6,7,8,9）默认右下
	 * @param int $opacity 透明度，默认0表示不进行透明
	 * @return void
	 */
	public function byImage($srcFilename, $waterFilename, $distFilename = NULL, $position = 9, $opacity = 0) {
		// 图片信息设置
		$this->setSrcFilename($srcFilename);
		$this->setWaterFilename($waterFilename);
		$distFilename and $this->setDistFilename($distFilename);
		$this->setPosition($position);
		
		if($opacity) {
			// 进行透明化
			imagecopymerge($this->srcImage['resource'], $this->waterImage['resource'], $this->position['x'], 
				$this->position['y'], 0, 0, $this->waterImage['width'], $this->waterImage['height'], $opacity);
		} else {
			// 不进行透明化
			imagecopy($this->srcImage['resource'], $this->waterImage['resource'], $this->position['x'], 
				$this->position['y'], 0, 0, $this->waterImage['width'], $this->waterImage['height']);
		}
		
		// 保存或者输出
		$this->saveOrOutput();
		
		// 销毁资源
		imagedestroy($this->srcImage['resource']);
		imagedestroy($this->waterImage['resource']);
	}
}