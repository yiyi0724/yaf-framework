<?php

/**
 * 表单上传
 * @author enychen
 */
namespace uploader;

class Form extends Uploader {

	/**
	 * 表单控件名称
	 * @var string
	 */
	protected $inputName = NULL;

	/**
	 * 构造函数
	 * @param string $inputName 上传名称
	 * @param string $directory 保存目录
	 * @throws \Exception
	 */
	public function __construct($inputName, $directory) {
		$this->setInputName($inputName);
		$this->setDirectory($directory);
	}
	
	/**
	 * 移动文件到上传位置
	 * @param string $tmpFile 上传临时存放文件绝对路径
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
	 * 设置表单控件名称
	 * @param string $inputName 表单控件名称
	 * @return Form $this 返回当前对象进行连贯操作
	 */
	protected function setInputName($inputName) {
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
		return $this->inputName;
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
			// 获取文件名
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
}