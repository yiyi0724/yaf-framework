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
	private $savePath = './uploads/';

	/**
	 * 文件保存名称
	 * @var string
	 */
	private $saveFilename;

	/**
	 * 设置文件支持类型
	 * @var array
	 */
	private $allowType = array('jpg', 'jpeg', 'gif', 'png');

	/**
	 * 设置文件限制大小（字节）
	 * @var int
	 */
	private $maxSize = 1024000;

	/**
	 * 设置是否随机重命名文件
	 * @var boolean
	 */
	private $isRandName = TRUE;

	/**
	 * 源文件名
	 * @var string
	 */
	private $originFilename;

	/**
	 * 临时文件名
	 * @var string
	 */
	private $tmpFilename;

	/**
	 * 文件类型
	 * @var string
	 */
	private $originFiletype;

	/**
	 * 文件大小
	 * @var string
	 */
	private $originFilesize;

	/**
	 * 保存文件名绝对路径
	 * @var string
	 */
	private $absSaveFilename;

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
	 * 是否是post传输
	 */
	private $isPost = TRUE;

	/**
	 * 设置保存的目录
	 * @param string $path 目录名称
	 * @return \File\Uploader 返回$this进行连贯操作
	 */
	public function setSavePath($savePath) {
		$this->savePath = rtrim($savePath, '/') . '/';
		return $this;
	}

	/**
	 * 设置保存的文件名，注意不要带格式后缀
	 * @param string $filename 保存的文件名
	 * @return \File\Uploader 返回$this进行连贯操作
	 */
	public function setSaveFilename($saveFilename) {
		$this->saveFilename = ltrim($saveFilename, '/');
		return $this;
	}

	/**
	 * 设置支持的文件类型，不需要.
	 * @param array $allowType 支持的文件名称
	 * @return \File\Uploader 返回$this进行连贯操作
	 */
	public function setAllowType(array $allowType) {
		$this->allowType = $allowType;
		return $this;
	}

	/**
	 * 设置限制文件上传大小（字节）
	 * @param int $maxSize 字节
	 * @return \File\Uploader 返回$this进行连贯操作
	 */
	public function setMaxSize($maxSize) {
		$this->maxSize = $maxSize;
		return $this;
	}

	/**
	 * 设置是否随机重命名文件
	 * @param bool $isRandName 是否随机重命名文件
	 * @return \File\Uploader 返回$this进行连贯操作
	 */
	public function setIsRandName($isRandName) {
		$this->isRandName = $isRandName;
		return $this;
	}

	/**
	 * 是否强制使用post传输
	 * @param bool $isPost 是否使用post进行传输
	 * @return \File\Uploader 返回$this进行连贯操作
	 */
	public function isPost($isPost) {
		$this->isPost = $isPost;
		return $this;
	}

	/**
	 * 设置单个属性
	 * @param string $key 键
	 * @param mixed  $val 值
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
		
		// 获取信息
		$name = $_FILES[$fileField]['name'];
		$tmpName = $_FILES[$fileField]['tmp_name'];
		$type = $_FILES[$fileField]['type'];
		$size = $_FILES[$fileField]['size'];
		$error = $_FILES[$fileField]['error'];
		
		// 设置文件信息
		if($return = $this->setOriginFilenameInfo($name, $tmpName, $type, $size, $error)) {
			// 上传之前先检查一下大小,类型,来源
			$return = $this->checkOriginFilesize() && $this->checkOriginFiletype() && $this->checkPost();
			if($return) {
				// 为上传文件设置新文件名
				$this->setAbsSaveFilename();
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
		foreach($name as $i=>$value) {
			// 设置文件信息
			if($this->setOriginFilenameInfo($name[$i], $tmpName[$i], $type[$i], $size[$i], $error[$i])) {
				// 上传之前先检查一下大小,类型,来源
				$return = $this->checkOriginFilesize() && $this->checkOriginFiletype() && $this->checkPost();
				if(!$return) {
					$errors[] = $this->getError();
					$return = FALSE;
				}
			} else {
				$errors[] = $this->getError();
				$return = FALSE;
			}
			// 如果有问题，则重新初使化属性
			if(!$return) {
				$this->setOriginFilenameInfo();
			}
		}
		
		if($return) {
			// 存放所有上传后文件名的变量数组
			$fileNames = array();
			// 通过检查，移动所有文件
			foreach($name as $i=>$value) {
				if($this->setOriginFilenameInfo($name[$i], $tmpName[$i], $type[$i], $size[$i], $error[$i])) {
					$this->setAbsSaveFilename($i + 1);
					if(!$this->copyFile()) {
						$errors[] = $this->getError();
						$return = FALSE;
					}
					$absSaveFilenames[] = $this->absSaveFilename;
				}
			}
			$this->setOption('absSaveFilename', $absSaveFilenames);
		}
		$this->setOption('errorMsg', $errors);
		
		return $return;
	}
	
	/**
	 * base64位图像上传
	 * @param  string $fileFile  上传文件的表单名称
	 * @return bool        		 如果上传成功返回数TRUE
	 */
	public function base64Upload($pictureStr) {
		// 目录是否可以写入(不存在尝试创建)
		if(!$this->isWritable()) {
			$this->setOption('errorMsg', $this->getError());
			return FALSE;
		}

		//匹配出图片的格式
		$isMatch = preg_match('/^(data:\s*image\/(\w+);base64,)/', $pictureStr, $result);
		if(!$isMatch) {
			$this->setOption('errorCode', 4);
			$this->setOption('errorMsg', $this->getError());
			return FALSE;
		}

		// 文件暂时保存
		$tmpFilename = $this->savePath.uniqid();
		$content = base64_decode(str_replace($result[1], NULL, $pictureStr));
		file_put_contents($tmpFilename, $content);
		$tmpFilenameInfo = stat($tmpFilename);

		// 文件类型
		$ext = $result[2] == 'jpeg' ? 'jpg' : $result[2];
		$name = "{$tmpFilename}.{$ext}";
		$tmpName = $tmpFilename;
		$type = "image/{$result[2]}";
		$size = $tmpFilenameInfo['size'];
		$error = 0;

		// 设置文件信息
		if($return = $this->setOriginFilenameInfo($name, $tmpName, $type, $size, $error)) {
			// 上传之前先检查一下大小,类型,来源
			if($return = $this->checkOriginFilesize() && $this->checkOriginFiletype()){
				// 移动文件
				$return = rename($tmpName, $name);
			}
		}

		// 上传错误
		if(!$return) {
			@unlink($tmpName);
			$this->setOption('errorMsg', $this->getError());
		}

		return $return;
	}

	/**
	 * 目录是否可以写入(不存在尝试创建)
	 * @return boolean
	 */
	private function isWritable() {
		if(empty($this->savePath)) {
			$this->setOption('errorCode', -3);
			return FALSE;
		}
		if(!is_dir($this->savePath) || !is_writable($this->savePath)) {
			if(!@mkdir($this->savePath, 0755, TRUE)) {
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
	 * 检查是否是合法的http post上传
	 * @param string $file 临时文件
	 * @return bool 是合法返回TRUE
	 */
	private function checkPost() {
		if($this->isPost && !is_uploaded_file($this->tmpFilename)) {
			$this->setOption('errorCode', 5);
			return FALSE;
		}
		return TRUE;
	}

	/**
	 * 获取上传后的文件名称
	 * @param  void   没有参数
	 * @return string 上传后，新文件的名称， 如果是多文件上传返回数组
	 */
	public function getFilename() {
		return $this->absSaveFilename;
	}

	/**
	 * 获取完整路径，可过滤前缀目录
	 * @param string $fliter 要过滤的目录
	 * @return string|array
	 */
	public function getAbsoluteFilename($fliter = NULL) {
		$isArray = is_array($this->absSaveFilename);
		$originFiles = $isArray ? $this->absSaveFilename : array($this->absSaveFilename);
		
		// 遍历拼接和过滤
		foreach($originFiles as $key=>$value) {
			$originFiles[$key] = $this->savePath . $value;
			if($fliter) {
				$originFiles[$key] = str_replace($fliter, NULL, $originFiles[$key]);
			}
		}
		
		return $isArray ? $originFiles : array_shift($originFiles);
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
		$str = "上传{$this->originFilename}出错:";
		switch($this->errorCode) {
			case 5:
				$str .= '上传方式有误';
				break;
			case 4:
				$str .= '没有文件被上传';
				break;
			case 3:
				$str .= "只有部分被上传";
				break;
			case 2:
			case 1:
				$str .= "上传的文件大小必须小于{$this->formatSize()}";
				break;
			case -1:
				$str .= '文件格式/文件类型不支持,仅支持' . implode(',', $this->allowType);
				break;
			case -2:
				// 移动文件到指定目录下失败
				$str .= "上传失败";
				break;
			case -3:
				// 未设置保存目录（代码错误）
				$str .= 'SERVER_ERROR_404';
				break;
			case -4:
				// 保存目录不存在或者服务器权限不够
				$str .= 'SERVER_ERROR_403';
				break;
			default:
				// 未知错误
				$str .= 'SERVER_ERROR_502';
		}
		return $str;
	}

	/**
	 * 设置和$_FILES有关的内容
	 * @param string $name 上传文件原始名称
	 * @param string $tmpName 临时目录文件名称
	 * @param string $type 文件类型
	 * @param int $size 文件大小
	 * @param int $error 上传错误代码
	 * @return boolean 如果上传没有错返回TRUE
	 */
	private function setOriginFilenameInfo($name = NULL, $tmpName = NULL, $type = NULL, $size = 0, $error = 0) {
		$this->setOption('originFilename', $name);
		if($error) {
			$this->setOption('errorCode', $error);
			return FALSE;
		}
		$this->setOption('tmpFilename', $tmpName);
		$originFiletype = explode('/', $type);
		$this->setOption('originFiletype', strtolower(array_pop($originFiletype)));
		$this->setOption('originFilesize', $size);
		return TRUE;
	}

	/**
	 * 设置上传后的文件名
	 * @param string $multiple 是否多文件上传
	 * @return void
	 */
	private function setAbsSaveFilename($suffix = NULL) {
		// 强迫症修改
		$originFiletype = ($this->originFiletype == 'jpeg') ? 'jpg' : $this->originFiletype;
		
		switch(TRUE) {
			case $this->saveFilename:
				// 用户自定义名称，多文件添加后缀
				$absSaveFilename = "{$this->saveFilename}{$suffix}.{$originFiletype}";
				break;
			case $this->isRandName:
				// 随机名称
				$fileName = uniqid();
				$absSaveFilename = "{$fileName}.{$originFiletype}";
				break;
			default:
				// 源文件名称
				$absSaveFilename = $this->originFilename;
		}
		
		$this->setOption('absSaveFilename', $absSaveFilename);
	}

	/**
	 * 检查文件是否类型符合
	 * 存在finfo扩展后，可以防止图片挂马问题，不存在请手动重新生成图片
	 * @return boolean
	 */
	private function checkOriginFiletype() {
		// 获取文件的后缀
		$mimeType = $this->originFiletype;
		
		// php5.4以后检查文件类型
		if(class_exists('\finfo', FALSE)) {
			$finfo = new \finfo(FILEINFO_MIME_TYPE);
			$mimeType = $finfo->file($this->tmpFilename);
			$mimeType = explode('/', $mimeType);
			$mimeType = strtolower(array_pop($mimeType));
		}
		
		// 格式是否允许
		if(!in_array($mimeType, $this->allowType)) {
			$this->setOption('errorCode', -1);
			return FALSE;
		}
		return TRUE;
	}

	/**
	 * 检查上传的文件是否是允许的大小
	 * @return boolean 没超过大小返回TRUE
	 */
	private function checkOriginFilesize() {
		if($this->originFilesize > $this->maxSize) {
			$this->setOption('errorCode', 2);
			return FALSE;
		}
		return TRUE;
	}

	/**
	 * 复制上传文件到指定的位置
	 * @return boolean
	 */
	private function copyFile($result = FALSE) {
		if(!$this->errorCode) {
			$result = @move_uploaded_file($this->tmpFilename, $this->savePath . $this->absSaveFilename);
			if(!$result) {
				$this->setOption('errorCode', -2);
			}
		}
		return $result;
	}

	/**
	 * 容量转换
	 * @return string
	 */
	private function formatSize() {
		$size = $this->maxSize;
		$units = array(
			'B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB'
		);
		for($i = 0, $len = count($units); $size >= 1024 && $i < $len; $i++) {
			$size /= 1024;
		}
		return round($size, 2) . $units[$i];
	}
}