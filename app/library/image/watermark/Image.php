<?php

/**
 * 水印图片类
 * @author enychen
 */
namespace image\watermark;

class Image {

	/**
	 * 水印图信息
	 * @var array
	 */
	protected $waterImage = NULL;

	/**
	 * 源图信息
	 * @var array
	 */
	protected $srcImage = NULL;

	/**
	 * 保存图信息
	 * @var array
	 */
	protected $distImage = NULL;

	/**
	 * 默认位置
	 * @var int
	 */
	protected $position = 9;

	/**
	 * 设置图片透明度
	 * @var int
	 */
	protected $opacity = 0;

	/**
	 * 设置水印图
	 * @param string $waterImage 水印图片绝对路径
	 * @return Watermark $this 返回当前对象进行连贯操作
	 */
	public function setWaterImage($waterImage) {
		// 分析信息
		$info = $this->analysisImageFile($waterImage);
		if(!$info) {
			$this->throws('水印图不存在或格式不支持');
		}
		
		// 保存信息
		$this->waterImage['width'] = $info[0];
		$this->waterImage['height'] = $info[1];
		$this->waterImage['resource'] = $this->openFile($waterImage);
		
		return $this;
	}

	/**
	 * 设置源图
	 * @param string $srcImage 原始图片绝对路径
	 * @return Watermark $this 返回当前对象进行连贯操作
	 */
	public function setSrcImage($srcImage) {
		// 分析信息
		$info = $this->analysisImageFile($srcImage);
		if(!$info) {
			$this->throws('原图图不存在或格式不支持');
		}
		
		// 分析图片格式
		$mineType = explode('/', $info['mime']);
		
		// 保存信息
		$this->srcImage['ext'] = $mineType[1];
		$this->srcImage['width'] = $info[0];
		$this->srcImage['height'] = $info[1];
		$this->srcImage['resource'] = $this->openFile($srcImage);
		
		return $this;
	}

	/**
	 * 设置目标图
	 * @param string $distImage 目标图片名称
	 * @return Watermark $this 返回当前对象进行连贯操作
	 */
	public function setDistImage($distImage) {
		// 文件信息分析
		$info = pathinfo($distImage);
		$path = $info['dirname'] . DIRECTORY_SEPARATOR;
		$ext = str_replace('jpg', 'jpeg', $info['extension']);
		
		// 保存目录判断是否存在
		if(!is_dir($path) || !is_writable($path)) {
			if(!@mkdir($path, 0755, TRUE)) {
				$this->throws('目标目录权限不足');
			}
		}
		
		// 保存信息
		$this->distImage['ext'] = $ext;
		$this->distImage['path'] = $path;
		$this->distImage['filename'] = $distImage;
		
		return $this;
	}

	/**
	 * 设置水印位置
	 * @param int $position 水印位置（1,2,3,4,5,6,7,8,9）
	 * @return Watermark $this 返回当前对象进行连贯操作
	 */
	public function setPosition($position) {
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
		
		$this->position = array(
			'x'=>$x, 'y'=>$y
		);
	}

	/**
	 * 设置透明度
	 * @param int $opacity 设置透明度
	 * @return Watermark $this 返回当前对象进行连贯操作
	 */
	public function setOpacity($opacity) {
		$this->opacity = $opacity;
		return $this;
	}

	/**
	 * 获取透明度
	 * @param int $opacity 设置透明度
	 * @return int
	 */
	public function getOpacity() {
		return $this->opacity;
	}

	/**
	 * 生成带水印的图片
	 * @return void
	 */
	public function create() {
		// 整理数据
		$params[] = $this->srcImage['resource'];
		$params[] = $this->waterImage['resource'];
		$params[] = $this->position['x'];
		$params[] = $this->position['y'];
		$params[] = 0;
		$params[] = 0;
		$params[] = $this->waterImage['width'];
		$params[] = $this->waterImage['height'];
		// 执行方法
		$method = 'imagecopy';
		if($opacity = $this->getOpacity()) {
			$method = 'imagecopymerge';
			$params[] = $opacity;
		}
		if(!call_user_func_array($method, $params)) {
			$this->throws('生成缩略图失败');
		}
		
		// 保存或者输出
		$this->saveOrOutput();
		
		// 销毁资源
		imagedestroy($this->srcImage['resource']);
		imagedestroy($this->waterImage['resource']);
	}

	/**
	 * 分析图片信息
	 */
	protected function analysisImageFile($filename) {
		// 分析图片信息
		$info = getimagesize($filename);
		if(!$info) {
			return FALSE;
		}
		
		return $info;
	}

	/**
	 * 载入图片资源
	 * @param string $filename 图片地址
	 * @return resource
	 */
	protected function openFile($filename) {
		return imagecreatefromstring(file_get_contents($filename));
	}

	/**
	 * 抛出异常信息
	 * @param string $message 异常信息
	 * @param int $code 异常码
	 * @throws \Exception
	 */
	protected function throws($message, $code = 0) {
		throw new \Exception($message, $code);
	}

	/**
	 * 图片输出或者进行保存
	 * @return
	 */
	private function saveOrOutput() {
		// 最终格式
		$ext = $this->distImage ? $this->distImage['ext'] : $this->srcImage['ext'];
		if(!function_exists("image{$ext}")) {
			$this->throws("无法保存成{$ext}格式的图片");
		}
		
		$params[] = $this->srcImage['resource'];
		if(!$this->distImage) {
			header("Content-Type: image/{$ext};charset=UTF-8");
		} else {
			$params[] = $this->distImage['filename'];
		}
		
		call_user_func_array("image{$ext}", $params);
	}
}