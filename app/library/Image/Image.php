<?php

namespace Image;

abstract class Image {
	
	/**
	 * 图片保存的目录
	 * @var string
	 */
	protected $path;
	
	/**
	 * 原始图片路径
	 * @var int
	 */
	protected $srcfilename;
	
	/**
	 * 原始图片宽度
	 * @var int
	 */
	protected $srcwidth = 0;
	
	/**
	 * 原始图片高度
	 * @var int
	 */
	protected $srcheight = 0;
	
	/**
	 * 原始图片格式
	 * @var int
	 */
	protected $srcType;
	
	/**
	 * 保存图片路径
	 * @var string
	 */
	protected $distfilename;
	
	/**
	 * 支持的格式
	 * @var int
	 */
	protected $allowType = array(1=>'gif', 2=>'jpeg', 3=>'png', 4=>'wbmp', 5=>'webp', 6=>'xbm', 7=>'xpm');

	/**
	 * 错误代码
	 * @var int
	 */
	protected $errorCode;

	/**
	 * 错误信息
	 * @var int
	 */
	protected $errorMsg;
	
	/**
	 * 错误信息通知
	 * @var array
	 */
	protected $errorNotify = array();
	
	/**
	 * 创建对象
	 */
	public function __construct() {
		// 错误整理
		$errorNotifys = array(
			'2000' => '源文件不存在',
			'2001' => '未设置保存目录',
			'2002' => '保存目录不存在或无操作权限',
			'2003' => '图片格式不支持转换',
			'2004' => '图片格式不支持保存',
			'2005' => '未设置保存文件名',
		);		
		foreach($this->errorNotify as $key=>$errorNotify) {
			if(!array_key_exists($key, $errorNotifys)) {
				$errorNotifys[$key] = $errorNotify;
			}
		}
		$this->errorNotify = $errorNotifys;
	}

	/**
	 * 连贯操作成员属性($path, $srcx, $srcy, $srcwidth,$srcheight, $distwidth, $distheight, $srcfilename, $disttype);
	 * @param string $key 属性名
	 * @param mixed $val 属性值
	 * @return \Image\Thumbnail 可以进行连续操作
	 */
	public final function set($key, $val) {
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
	protected final function setOption($key, $val) {
		$this->$key = $val;
	}

	/**
	 * 目录是否可以写入(不存在尝试创建)
	 * @return boolean
	 */
	protected final function isWritable() {
		// 源文件是否存在
		if(!is_file($this->srcfilename)) {
			$this->setOption('errorCode', 2000);
			return FALSE;
		}
		
		// 存储目录是否存在
		if(empty($this->path)) {
			$this->setOption('errorCode', 2001);
			return FALSE;
		}
		if(!is_dir($this->path) || !is_writable($this->path)) {
			if(!@mkdir($this->path, 0755, TRUE)) {
				$this->setOption('errorCode', 2002);
				return FALSE;
			}
		}
		return TRUE;
	}
	
	/**
	 * 原图片信息分析
	 * @return boolean 分析成功返回TRUE
	 */
	protected final function setSrcFilename() {
		// 分析图片信息
		$imageInfo = getimagesize($this->srcfilename);
	
		// 支持的格式
		if(empty($this->allowType[$imageInfo[2]])) {
			$this->setOption('errorCode', 2003);
			return FALSE;
		}
	
		// 设置属性值
		$this->setOption('srcwidth', $this->srcwidth ? : $imageInfo[0]);
		$this->setOption('srcheight', $this->srcheight ? : $imageInfo[1]);
		$this->setOption('srcType', $this->allowType[$imageInfo[2]]);
	
		return TRUE;
	}

	/**
	 * 根据错误代码转成错误信息
	 * @return string
	 */
	protected final function getError() {
		if(in_array($this->errorCode, $this->errorNotify)) {
			return $this->errorNotify[$this->errorCode];
		}
		return '未知错误';
	}
}