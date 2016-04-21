<?php
/**
 * 缩略裁剪图
 * @author enychen
 * @version 1.0
 */
namespace Ku\Eny;

class Thumbnail {
	
	/**
	 * 图片保存的目录
	 * @var string
	 */
	private $path = './thumbs/';
	
	/**
	 * 图片起始x标点
	 * @var int
	 */
	private $srcx = 0;
	
	/**
	 * 原图片起始y标点
	 * @var int
	 */
	private $srcy = 0;

	/**
	 * 原始图片宽度
	 * @var int
	 */
	private $srcwidth = 0;
	
	/**
	 * 原始图片高度
	 * @var int
	 */
	private $srcheight = 0;
	
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
	 * 支持缩略图的格式
	 * @var int
	 */
	private $allowType = array(1=>'gif', 2=>'jpeg', 3=>'png', 4=>'wbmp', 5=>'webp', 6=>'xbm', 7=>'xpm');
	
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
	 * 原始图片路径
	 * @var int
	 */
	private $srcfilename;
	
	/**
	 * 缩略图存放地址
	 * @var string
	 */
	private $distfilename;
	
	/**
	 * 等比例进行缩放
	 * @var unknown
	 */
	private $eometric = 0;
	
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
	
	/**
	 * 连贯操作成员属性($path, $srcx, $srcy, $srcwidth,$srcheight, $distwidth, $distheight, $srcfilename, $disttype);
	 * @param string $key 属性名
	 * @param mixed $val 属性值
	 * @return \Image\Thumbnail 可以进行连续操作
	 */
	public function set($key, $val) {
		$key = strtolower($key);
		if(array_key_exists($key, get_class_vars(get_class($this)))) {
			$this->setOption($key, $val);
		}
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
	 * 创建缩略图
	 * @return bool 创建成功返回TRUE
	 */
	public function create() {
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
				$str = '文件不存在';
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
		if(!is_file($this->srcfilename)) {
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
	 * 原图片信息分析
	 * @return boolean 分析成功返回TRUE
	 */
	private function setSrcInfo() {
		// 分析图片信息
		$imageInfo = getimagesize($this->srcfilename);
		
		// 支持的格式
		if(empty($this->allowType[$imageInfo[2]])) {
			$this->setOption('errorCode', -4);
			return FALSE;
		}
		
		// 设置属性值
		$this->setOption('srcwidth', $this->srcwidth ? : $imageInfo[0]);
		$this->setOption('srcheight', $this->srcheight ? : $imageInfo[1]);		
		$this->setOption('srcType', $this->allowType[$imageInfo[2]]);
		
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