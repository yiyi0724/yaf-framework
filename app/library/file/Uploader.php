<?php

/**
 * 文件上传类
 * @author enychen
 */
namespace file;

class Uploader {

	/**
	 * 表单控件名称
	 * @var string
	 */
	protected $inputName = NULL;

	/**
	 * base64上传字符串
	 * @var string
	 */
	protected $base64Str = NULL;

	/**
	 * 远程图片上传
	 * @var string
	 */
	protected $pictureUrl = NULL;

	/**
	 * 允许的类型
	 * @var string
	 */
	protected $allowType = array('jpg','png');

	/**
	 * 存放的目录
	 * @var string
	 */
	protected $directory = NULL;

	/**
	 * 文件最大的大小，单位BYTE，默认最大1MB
	 * @var int
	 */
	protected $maxSize = 1048576;

	/**
	 * 保存文件名
	 * @var string
	 */
	protected $filename = NULL;

	/**
	 * 文件名后缀
	 * @var string
	 */
	protected $ext = NULL;

	/**
	 * 抛出异常
	 * @param string $message 异常信息
	 * @param int $code 异常码
	 * @throws \Exception
	 */
	protected function throws($message, $code = 0) {
		throw new \Exception($message, $code);
	}

	/**
	 * 容量大小单位转换
	 * @param string $size 大小，单位BYTE
	 * @return string
	 */
	protected function formatSize($size) {
		$units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB');
		for($i = 0, $len = count($units); $size >= 1024 && $i < $len; $i++) {
			$size /= 1024;
		}
		return round($size, 2) . $units[$i];
	}

	/**
	 * 检查文件上传错误
	 * @param int $error $_FILES的error字段
	 * @throws \Exception
	 * @return void
	 */
	protected function checkError($error) {
		if($error) {
			$this->throws('文件上传有误');
		}
	}

	/**
	 * 检查文件格式
	 * @param string $tmpFile 上传临时存放文件绝对路径
	 * @throws \Exception
	 * @return void
	 */
	protected function checkAllowType($tmpFile) {
		// 目前支持的类型
		$allowTypes = array(
			'png' => 'image/png',
			'jpg' => 'image/jpeg',
			'jpeg' => 'image/jpeg',
			'doc' => 'application/msword',
			'ppt' => 'application/vnd.ms-powerpoint',
			'xls' => 'application/vnd.ms-excel',
			'wps' => 'application/vnd.ms-works',
			'ico' => 'image/x-icon',
			'icon' => 'image/x-icon',
		);

		// 获取设定的类型
		$allows = array();
		foreach($this->getAllowType() as $value) {
			if(isset($allowTypes[$value])) {
				$allows[] = $allowTypes[$value];
			}
		}

		// 检查格式
		$finfo = new \finfo(FILEINFO_MIME_TYPE);
		$mimeType = $finfo->file($tmpFile);		
		if(!in_array($mimeType, $allows)) {
			@unlink($tmpFile);	// 如果格式不允许，则删除文件
			$this->throws(sprintf("只允许上传%s类型的文件", implode(',', $this->getAllowType())));
		}
	}

	/**
	 * 检查文件大小
	 * @param int $size 文件大小
	 * @throws \Exception
	 * @return void
	 */
	protected function checkFilesize($size) {
		if($this->getMaxSize() != -1 && $this->getMaxSize() < $size) {
			@unlink($tmpFile);	// 如果格式不允许，则删除文件
			$this->throws("文件最大允许{$this->formatSize($this->getMaxSize())}");
		}
	}

	/**
	 * 检查文件是否是post传输
	 * @param string $tmpFile 上传临时存放文件绝对路径
	 * @throws \Exception
	 * @return void
	 */
	private function checkIsPost($tmpFile) {
		if(!is_uploaded_file($tmpFile)) {
			$this->throws('非法访问');
		}
	}

	/**
	 * 移动文件到上传位置
	 * @param string $tmpName 上传临时存放文件绝对路径
	 * @throws \Exception
	 * @return string 文件绝对路径
	 */
	protected function move($tmpFile) {
		$fullFilename = $this->getFullFilename();
		if(!@move_uploaded_file($tmpFile, $fullFilename)) {
			@unlink($tmpFile);
			$this->throws('系统发生错误，上传文件失败');
		}
		
		return $fullFilename;
	}

	/**
	 * 设置文件的后缀
	 * @param string $filename 文件原始名称
	 * @return Uploader $this 返回当前对象进行连贯操作
	 */
	protected function setExt($filename) {
		$this->ext = strrchr($filename, '.');
		return $this;
	}

	/**
	 * 获取后缀名
	 * @return string
	 */
	protected function getExt() {
		return str_replace('jpeg', 'jpg', $this->ext);
	}

	/**
	 * 获取完整的文件名
	 * @return string
	 */
	protected function getFullFilename() {
		return sprintf('%s%s%s', $this->getDirectory(), ($this->getFilename() ?  : uniqid() . mt_rand(100, 999)), $this->getExt());
	}
	
	/**
	 * 设置表单控件名称
	 * @param string $inputName 表单控件名称
	 * @return Uploader $this 返回当前对象进行连贯操作
	 */
	public function setInputName($inputName) {
		if(empty($_FILES[$inputName])) {
			$this->throws('上传文件不存在');
		}

		$this->inputName = $inputName;
		return $this;
	}

	/**
	 * 获取表单控件名称
	 * @return string
	 */
	public function getInputName() {
		if(!$this->inputName) {
			throw new \Exception('请设置inputName');
		}
		return $this->inputName;
	}

	/**
	 * 设置base64上传字符串
	 * @param string $base64Str 字符串
	 * @return Uploader $this 返回当前对象进行连贯操作
	 */
	public function setBase64Str($base64Str) {
		$this->base64Str = $base64Str;
		return $this;
	}

	/**
	 * 获取base64上传字符串
	 * @return string
	 */
	public function getBase64Str() {
		if(!$this->base64Str) {
			throw new \Exception('请设置base64Str');
		}
		return $this->base64Str;
	}

	/**
	 * 设置远程图像地址
	 * @param string $pictureUrl 远程图片地址
	 * @return Uploader $this 返回当前对象进行连贯操作
	 */
	public function setPictureUrl($pictureUrl) {
		$this->pictureUrl = $pictureUrl;
		return $this;
	}

	/**
	 * 获取远程图像地址
	 * @return string
	 */
	public function getPictureUrl() {
		return $this->pictureUrl;
	}
	
	/**
	 * 设置允许的文件格式
	 * @param string $allowType 允许格式，多个用,隔开
	 * @return Uploader $this 返回当前对象进行连贯操作
	 */
	public function setAllowType($allowType) {
		$this->allowType = explode(',', $allowType);
		return $this;
	}

	/**
	 * 获取允许的文件格式
	 * @return array
	 */
	public function getAllowType() {
		return $this->allowType;
	}

	/**
	 * 设置文件保存的目录
	 * @param string $directory 文件保存的目录
	 * @return Uploader $this 返回当前对象进行连贯操作
	 */
	public function setDirectory($directory) {
		// 是不是一个目录，不是的话创建目录
		if(!is_dir($directory)) {
			@mkdir($directory, 0755, TRUE);
		}
		// 判断目录是否有写的权限
		if(!is_writable($directory)) {
			$this->throws('目录不可写');
		}
		
		$this->directory = $directory;
		return $this;
	}

	/**
	 * 获取文件保存的目录
	 * @throws \Exception
	 * @return string
	 */
	public function getDirectory() {
		if(!$this->directory) {
			$this->throws('请设置directory');
		}
		return $this->directory;
	}

	/**
	 * 设置文件最大允许的大小
	 * @param int $maxSize 文件大小，-1表示不设置大小
	 * @return Uploader $this 返回当前对象进行连贯操作
	 */
	public function setMaxsize($maxSize) {
		$this->maxSize = intval($maxSize);
		return $this;
	}

	/**
	 * 获取文件最大允许的大小
	 * return int
	 */
	public function getMaxSize() {
		return $this->maxSize;
	}

	/**
	 * 设置文件保存名称
	 * @param string $filename 文件名，不包含目录和后缀
	 * @return Uploader $this 返回当前对象进行连贯操作
	 */
	public function setFilename($filename) {
		$this->filename = $filename;
		return $this;
	}

	/**
	 * 获取文件保存名称
	 * @return string
	 */
	public function getFilename() {
		return $this->filename;
	}

	/**
	 * 单文件上传
	 * @return string 文件绝对路径
	 */
	public function single() {
		// 获取上传的文件
		$file = $_FILES[$this->getInputName()];
		// 文件来源检查
		$this->checkIsPost($file['tmp_name']);
		// 文件上传是否有错误
		$this->checkError($file['error']);
		// 文件类型检查
		$this->checkAllowType($file['tmp_name']);
		// 文件大小检查
		$this->checkFilesize($file['size']);
		// 设置后缀名
		$this->setExt($file['name']);
		// 上传文件
		return $this->move($file['tmp_name']);
	}

	/**
	 * 多文件上传
	 * @return array 文件绝对路径数组
	 */
	public function multiple() {
		// 获取上传的文件
		$lists = array();
		if($files = $_FILES[$this->getInputName()]) {			
			$filename = $this->getFilename();
			for($i=0, $len=count($files['name']); $i<$len; $i++) {
				// 文件来源检查
				$this->checkIsPost($files['tmp_name'][$i]);
				// 文件上传是否有错误
				$this->checkError($files['error'][$i]);
				// 文件类型检查
				$this->checkAllowType($files['tmp_name'][$i]);
				// 文件大小检查
				$this->checkFilesize($files['size'][$i]);
				// 设置保存名
				$filename and $this->setFilename("{$filename}_{$i}");
				// 设置后缀名
				$this->setExt($files['name'][$i]);
				// 上传文件
				$lists[] = $this->move($files['tmp_name'][$i]);
			}
		}
		return $lists;
	}

	/**
	 * base64位图像上传
	 * @return string 返回文件路径
	 */
	public function base64() {
		//匹配出图片的格式
		$isMatch = preg_match('/^(data:\s*image\/(\w+);base64,)/', $this->getBase64Str(), $result);
		if(!$isMatch) {
			$this->throws('没有文件被上传');
		}
		// 解析图片信息
		$this->setExt("base64.{$result[2]}");
		// 获取绝对路径文件名
		$fullFilename = $this->getFullFilename();
		// 保存图片
		file_put_contents($fullFilename, base64_decode(str_replace($result[1], NULL, $this->getBase64Str())));
		// 检查文件大小
		$this->checkFilesize($fullFilename);
		// 检查文件类型
		$this->checkAllowType($fullFilename);
		// 返回图像绝对路径
		return $fullFilename;
	}

	/**
	 * 通过url地址获取上传文件
	 * @return boolean 上传成功返回TRUE
	 */
	public function url() {
		$origin = @file_get_contents($this->getPictureUrl(), FALSE);
		if(!$origin) {
			$this->throws('图像地址有误');
		}
		// 解析图片信息
		$this->setExt(basename($origin));
		// 获取绝对路径文件名
		$fullFilename = $this->getFullFilename();
		// 保存图片
		file_put_contents($fullFilename, $origin);
		// 检查文件大小
		$this->checkFilesize($fullFilename);
		// 检查文件类型
		$this->checkAllowType($fullFilename);
		// 返回图像绝对路径
		return $fullFilename;
	}
}