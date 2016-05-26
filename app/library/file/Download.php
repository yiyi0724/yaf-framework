<?php

/**
 * 文件下载类
 * @author enychen
 * @version 1.0
 */
namespace File;

class Download {

	/**
	 * 配置选项
	 * @var array
	 */
	protected $option = array(
		'data'=>null, 
		'downloadName'=>null, 
		'headers'=>array()
	);

	/**
	 * 设置要输出的数据
	 * @param string 数据 $data
	 */
	public function setData($data) {
		$this->option['data'] = $data;
	}

	/**
	 * 读取文件作为输出的数据
	 * @param string 文件名 $filename
	 */
	public function setDataFromFile($filename) {
		ob_start();
		readfile($filename);
		$this->option['data'] = ob_get_clean();
	}

	/**
	 * 设置下载的文件名
	 * @param string 下载的文件名 $downloadName
	 */
	public function setDownloadName($downloadName) {
		$this->option['downloadName'] = $downloadName;
	}

	/**
	 * 需要补充的header头
	 * @param array 相应头 $headers
	 */
	public function setHeader(array $headers) {
		$this->option['headers'] = $headers;
	}

	/**
	 * 输出
	 */
	public function output() {
		header('Content-Description: File Transfer');
		header("Accept-Ranges:bytes");
		header('Content-Type: application/octet-stream');
		header('Content-Transfer-Encoding: binary');
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Pragma: public');
		header("Content-Disposition: attachment; filename={$this->option['downloadName']}");
		header('Content-Length: ' . strlen($this->option['data']));
		foreach($this->option['headers'] as $header) {
			header($header);
		}
		exit($this->option['data']);
	}
}