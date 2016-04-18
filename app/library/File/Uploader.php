<?php
/**
 * 文件上传类(支持单，多上传方式)
 * @author enychen
 * @version 1.0
 */
namespace File;

class Uploader {

	/**
	 * 文件保存目录
	 * @var string
	 */
	private $path = './uploads/';

	/**
	 * 设置限制上传文件的类型
	 * @var array
	 */
	private $allowtype = array('jpeg', 'gif', 'png');

	/**
	 * 限制文件上传大小（字节）
	 * @var int
	*/
	private $maxsize = 1024000;

	/**
	 * 设置是否随机重命名文件
	 * @var boolean
	 */
	private $israndname = TRUE;

	/**
	 * 源文件名
	 * @var string
	 */
	private $originName;

	/**
	 * 临时文件名
	 * @var string
	 */
	private $tmpFileName;

	/**
	 * 文件类型(文件后缀)
	 * @var string
	 */
	private $fileType;

	/**
	 * 文件大小
	 * @var string
	 */
	private $fileSize;

	/**
	 * 新文件名
	 * @var string
	 */
	private $newfilename;

	/**
	 * 错误号
	 * @var int
	 */
	private $errorCode = 0;

	/**
	 * 错误报告消息
	 * @var string
	 */
	private $errorMsg = NULL;

	/**
	 * 用于设置成员属性（$path, $allowtype, $maxsize, $israndname, $newfilename）
	 * 可以通过连贯操作一次设置多个属性值
	 * @param  string $key    成员属性名(不区分大小写)
	 * @param  mixed  $val    为成员属性设置的值
	 * @return \File\Uploader 返回自己对象$this，可以用于连贯操作
	 */
	public function set($key, $val) {
		$key = strtolower($key);
		if(array_key_exists($key, get_class_vars(get_class($this)))) {
			$this->setOption($key, $val);
		}
		return $this;
	}

	/**
	 * 为单个成员属性设置值
	 * @param string $key 键
	 * @param string $val 值
	 * @return void
	 */
	private function setOption($key, $val) {
		$this->$key = $val;
	}

	/**
	 * 单文件上传
	 * @param  string $fileFile  上传文件的表单名称
	 * @return bool        		 如果上传成功返回数TRUE
	 */
	public function singleUpload($fileField, $return = TRUE) {
		// 目录是否可以写入(不存在尝试创建)
		if(!$this->isWritable()) {
			$this->setOption('errorMsg', $this->getError());
			return FALSE;
		}

		// 检查$_FILE是否存在此字段上传信息
		if(!$this->isExistsFile($fileField)) {
			$this->setOption('errorMsg', $this->getError());
			return FALSE;
		}
		$name = $_FILES[$fileField]['name'];
		$tmpName = $_FILES[$fileField]['tmp_name'];
		$type = $_FILES[$fileField]['type'];
		$size = $_FILES[$fileField]['size'];
		$error = $_FILES[$fileField]['error'];

		// 设置文件信息
		if($return = $this->setFiles($name, $tmpName, $size, $error)) {
			// 上传之前先检查一下大小和类型
			if($return = $this->checkFileSize() && $this->checkFileType()) {
				// 为上传文件设置新文件名
				$this->setNewFileName();
				// 移动文件是否成功
				$return = $this->copyFile();
			}
		}
			
		// 上传错误
		if(!$return) {
			$this->setOption('errorMsg', $this->getError());
		}

		return $return;
	}
	
	/**
	 * 多文件上传
	 * @param  string $fileFile  上传文件的表单名称
	 * @return bool        		 如果上传成功返回数TRUE
	 */
	public function multipleUpload($fileField, $return = TRUE) {
		// 目录是否可以写入(不存在尝试创建)
		if(!$this->isWritable()) {
			$this->setOption('errorMsg', $this->getError());
			return FALSE;
		}
	
		// 检查$_FILE是否存在此字段上传信息
		if(!$this->isExistsFile($fileField, TRUE)) {
			$this->setOption('errorMsg', $this->getError());
			return FALSE;
		}
		$name = $_FILES[$fileField]['name'];
		$tmpName = $_FILES[$fileField]['tmp_name'];
		$type = $_FILES[$fileField]['type'];
		$size = $_FILES[$fileField]['size'];
		$error = $_FILES[$fileField]['error'];

		$errors = array();
		for($i = 0; $i < count($name); $i++) {
			// 设置文件信息
			if($this->setFiles($name[$i], $tmpName[$i], $size[$i], $error[$i])) {
				// 进行文件检查
				if(!$this->checkFileSize() || !$this->checkFileType()) {
					$errors[] = $this->getError();
					$return = FALSE;
				}
			} else {
				$errors[] = $this->getError();
				$return = FALSE;
			}
			// 如果有问题，则重新初使化属性
			if(!$return) {
				$this->setFiles();
			}
		}

		if($return) {
			// 存放所有上传后文件名的变量数组
			$fileNames = array();
			// 通过检查，移动所有文件
			for($i = 0; $i < count($name); $i++) {
				if($this->setFiles($name[$i], $tmpName[$i], $size[$i], $error[$i])) {
					$this->setNewFileName($i+1);
					if(!$this->copyFile()) {
						$errors[] = $this->getError();
						$return = FALSE;
					}
					$fileNames[] = $this->newfilename;
				}
			}
			$this->setOption('newfilename', $fileNames);
		}
		$this->setOption('errorMsg', $errors);
	
		return $return;
	}

	/**
	 * 获取上传后的文件名称
	 * @param  void   没有参数
	 * @return string 上传后，新文件的名称， 如果是多文件上传返回数组
	 */
	public function getFileName() {
		return rtrim($this->path, '/') . '/' . $this->newfilename;
	}

	/**
	 * 上传失败后，调用该方法则返回，上传出错信息
	 * @param  void   没有参数
	 * @return string  返回上传文件出错的信息报告，如果是多文件上传返回数组
	 */
	public function getErrorMsg() {
		return $this->errorMsg;
	}

	/* 设置上传出错信息 */
	private function getError() {
		$str = "上传文件{$this->originName}时出错 : ";
		switch($this->errorCode) {
			case 5:
				$str .= '上传方式有误';
				break;
			case 4:
				$str .= "没有文件被上传";
				break;
			case 3:
				$str .= "文件只有部分被上传";
				break;
			case 2:
				$str .= "上传文件的大小超过了HTML表单中MAX_FILE_SIZE选项指定的值";
				break;
			case 1:
				$str .= "上传的文件超过了php.ini中upload_max_filesize选项限制的值";
				break;
			case -1:
				$str .= '只允许上传' . implode(',', $this->allowtype) . '格式的文件';
				break;
			case -2:
				$str .= "文件过大,上传的文件不能超过{$this->maxsize}个字节";
				break;
			case -3:
				$str .= "上传失败";
				break;
			case -4:
				$str .= 'SERVER_ERROR_403';
				break;
			case -5:
				$str .= 'SERVER_ERROR_404';
				break;
			default:
				$str .= 'SERVER_ERROR_502';
		}
		return $str;
	}

	/**
	 * 设置和$_FILES有关的内容
	 * @param string $name 上传文件原始名称
	 * @param string $tmpName 临时目录文件名称
	 * @param int $size 文件大小
	 * @param int $error 上传错误代码
	 * @return boolean 如果上传没有错返回TRUE
	 */
	private function setFiles($name = NULL, $tmpName = NULL, $size = 0, $error = 0) {
		if($error) {
			$this->setOption('errorCode', $error);
			return FALSE;
		}
		$this->setOption('originName', $name);
		$this->setOption('tmpFileName', $tmpName);
		$fileType = explode('.', $name);
		$this->setOption('fileType', strtolower(array_pop($fileType)));
		$this->setOption('fileSize', $size);
		return TRUE;
	}

	/**
	 * 设置上传后的文件名
	 * @param string $multiple 是否多文件上传
	 * @return void
	 */
	private function setNewFileName($suffix = NULL) {
		switch(TRUE) {
			case $this->newfilename:
				// 用户自定义名称，多文件添加后缀
				$this->setOption('newfilename', $this->suffixName($suffix));
				break;
			case $this->israndname:
				// 随机名称
				$this->setOption('newfilename', $this->proRandName());
				break;
			default:
				// 源文件名称
				$this->setOption('newfilename', $this->originName);
		}
	}

	/**
	 * 检查文件是否类型符合
	 * 存在finfo扩展后，可以防止图片挂马问题，不存在请手动重新生成图片
	 * @return boolean
	 */
	private function checkFileType() {
		// 获取文件的后缀
		$mimeType = $this->fileType;

		// php5.4以后检查文件类型
		if(class_exists('finfo')) {
			$finfo = new \finfo(FILEINFO_MIME_TYPE);
			$mimeType = $finfo->file($this->tmpFileName);
			$mimeType = explode('/', $mimeType);
			$mimeType = strtolower(array_pop($mimeType));
		}

		// 格式是否允许
		if(!in_array($mimeType, $this->allowtype)) {
			$this->setOption('errorCode', -1);
			return FALSE;
		}
		return TRUE;
	}

	/**
	 * 检查上传的文件是否是允许的大小
	 * @return boolean 没超过大小返回TRUE
	 */
	private function checkFileSize() {
		if($this->fileSize > $this->maxsize) {
			$this->setOption('errorCode', -2);
			return FALSE;
		}
		return TRUE;
	}

	/**
	 * 目录是否可以写入(不存在尝试创建)
	 * @return boolean
	 */
	private function isWritable() {
		if(empty($this->path)) {
			$this->setOption('errorCode', -5);
			return FALSE;
		}
		if(!is_dir($this->path) || !is_writable($this->path)) {
			if(!@mkdir($this->path, 0755, TRUE)) {
				$this->setOption('errorCode', -4);
				return FALSE;
			}
		}
		return TRUE;
	}

	/**
	 * 检查$_FILE是否存在此字段上传信息
	 * @param string $fileFile  上传文件的表单名称
	 * @param boolean $multiple 是否多文件上传
	 * @return boolean 有上传表单字段返回TRUE
	 */
	private function isExistsFile($fileField, $multiple = FALSE) {
		// 文件是否存在
		if(empty($_FILES[$fileField])) {
			$this->setOption('errorCode', 4);
			return FALSE;
		}
		// 是否多文件
		if($multiple && (!is_array($_FILES[$fileField]['name']))) {
			$this->setOption('errorCode', 5);
			return FALSE;
		}
		
		return TRUE;
	}

	/**
	 * 设置随机文件名
	 * @return string 随机的文件名
	 */
	private function proRandName() {
		$fileName = date('YmdHis') . '_' . rand(1000, 9999);
		$fileType = ($this->fileType == 'jpeg') ? 'jpg' : $this->fileType;
		return "{$fileName}.{$fileType}";
	}

	/**
	 * 多文件上传自动加上后缀名
	 * @param int $suffix 后缀递增值
	 * @return string
	 */
	private function suffixName($suffix) {
		$suffix = $suffix ? "_{$suffix}" : NULL;
		$fileType = ($this->fileType == 'jpeg') ? 'jpg' : $this->fileType;
		return "{$this->newfilename}{$suffix}.{$fileType}";
	}

	/**
	 * 复制上传文件到指定的位置
	 * @return boolean
	 */
	private function copyFile() {
		if(!$this->errorCode) {
			$path = rtrim($this->path, '/') . '/';
			$path .= $this->newfilename;
			if(@move_uploaded_file($this->tmpFileName, $path)) {
				return TRUE;
			}
				
			$this->setOption('errorCode', -3);
			return FALSE;
		}
		return FALSE;
	}
}