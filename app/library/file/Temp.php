<?php

class Uploader {

	protected $inputName = NULL;

	protected $allowType = NULL;

	protected $directory = NULL;

	protected $filesize = 0;

	protected $filename = NULL;

	protected $ext = NULL;

	public function __construct($inputName, $allowType, $filesize, $directory) {
		$this->setInputName($inputName);
		$this->setAllowType($allowType);
		$this->setFilsize($filesize);
		$this->setDirectory($directory);
	}

	protected function throws($message, $code = 0) {
		throw new \Exception($message, $code);
	}

	protected function formatSize($size) {
		$units = array( 'B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB');
		for($i = 0, $len = count($units); $size >= 1024 && $i < $len; $i++) {
			$size /= 1024;
		}
		return round($size, 2) . $units[$i];
	}

	protected function checkError($error) {
		if($error) {
			$this->throws('文件上传有误', 1006);
		}
	}

	protected function checkAllowType($tmpFile) {
		$finfo = new \finfo(FILEINFO_MIME_TYPE);
		$mimeType = $finfo->file($tmpFile);
		$mimeType = explode('/', $mimeType);
		$mimeType = strtolower(array_pop($mimeType));

		// 格式是否允许
		if(!in_array($mimeType, $this->getAllowType())) {
			$this->throws(sprintf("只允许上传%s类型的文件", implode(',', $this->getAllowType())), 1007);
		}
	}

	protected function checkFilesize($size) {
		if($this->getFilesize() < $size) {
			$this->throws("文件最大允许{$this->formatSize($this->getFilesize())}", 1007);
		}
	}

	/**
	 * 移动文件到上传位置
	 * @return boolean
	 */
	protected function move($tmpName) {
		$result = @move_uploaded_file($tmpName, $this->getFilename());
		if(!$result) {
			$this->throws('系统发生错误，上传文件失败', 10008);
		}
	}

	protected function setExt($filename) {
		$this->ext = strrchr($filename, '.');
	}

	protected function getExt() {
		return $this->ext;
	}

	public function setInputName($inputName) {
		if(empty($_FILES[$inputName])) {
			$this->throws('上传文件不存在', 1000);
		}

		$this->inputName = $inputName;
		return $this;
	}

	public function getInputName() {
		return $this->inputName;
	}

	public function setAllowType($allowType) {
		$this->allowType = explode(',', str_replace('jpg', 'jpeg', strtolower($allowType)));
		return $this;
	}

	public function getAllowType() {
		return $this->allowType;
	}

	public function setDirectory($directory) {
		// 是不是一个目录，不是的话创建目录
		if(!is_dir($directory)) {
			@mkdir($directory, 0755, TRUE);
		}
		// 判断目录是否有写的权限
		if(!is_writable($directory)) {
			$this->throws('目录不可写', 1001);
		}

		$this->directory = $directory;
		return $this;
	}

	public function getDirectory() {
		return $this->directory;
	}

	public function setFilsize($filesize) {
		$this->filesize = $filesize;
		return $this;
	}

	public function getFilesize() {
		return $this->filesize;
	}

	public function setFilename($filename) {
		$this->filename = $filename;
		return $this;
	}

	public function getFilename() {
		return sprintf('%s%s%s', $this->getDirectory(), ($this->filename ? : uniqid() . mt_rand(10000, 99999)), $this->getExt());
	}

	/**
	 * 单文件上传
	 */
	public function single() {
		// 获取上传的文件
		$file = $_FILES[$this->getInputName()];
		// 文件上传是否有错误
		$this->checkError($file['error']);
		// 文件类型检查
		$this->checkAllowType($file['tmp_name']);
		// 文件大小检查
		$this->checkFilesize($file['size']);
		// 设置后缀名
		$this->setExt($file['name']);
		// 上传文件
		$this->move($file['tmp_name']);

		return TRUE;
	}

	/**
	 * 多文件上传
	 */
	public function multiple() {
		// 获取上传的文件
		$files = $_FILES[$this->getInputName()];
		foreach($files as $file) {
			// 文件上传是否有错误
			$this->checkError($file['error']);
			// 文件类型检查
			$this->checkAllowType($file['tmp_name']);
			// 文件大小检查
			$this->checkFilesize($file['size']);
			// 设置后缀名
			$this->setExt($file['name']);
			// 上传文件
			$this->move($file['tmp_name']);
		}

		return TRUE;
	}
}


try {
	$uploader = new Uploader('upload', 'jpeg,png', 102400000, __DIR__ . '/');
	$uploader->single();
	echo $uploader->getFilename();
} catch(\Exception $e) {
	exit($e->getMessage());
}