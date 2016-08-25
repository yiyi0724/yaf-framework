<?php

/**
 * 文件存储类
 * @author enychen
 */
namespace wxsdk\storage;

class File extends Adapter {

	/**
	 * 文件名
	 * @var string
	 */
	protected $filename = NULL;

	/**
	 * 文件内容
	 * @var array
	 */
	protected $content = array();

	/**
	 * 构造函数
	 * @param string $filename 文件名
	 */
	public function __construct($filename) {
		$this->setFilename($filename)->setContent();
	}

	/**
	 * 设置文件名
	 * @param string $filename 文件名
	 * @return File $this 返回当前对象进行连贯操作
	 */
	protected function setFilename($filename) {
		$this->filename = sprintf("%s/tmp/%s", __DIR__, $filename);
		if(!is_file($this->filename)) {
			file_put_contents($this->filename, json_encode(array()));
		}
		return $this;
	}

	/**
	 * 获取文件名
	 * @return string
	 */
	protected function getFilename() {
		return $this->filename;
	}

	/**
	 * 设置文件内容
	 * @return File $this 返回当前对象进行连贯操作
	 */
	protected function setContent() {
		$this->content = json_decode(file_get_contents($this->getFilename()), TRUE);
		return $this;
	}

	/**
	 * 获取文件内容
	 * @return array
	 */
	protected function getContent() {
		return $this->content;	
	}

	/**
	 * 保存内容到文件中
	 * @return void
	 */
	protected function saveContent() {
		file_put_contents($this->getFilename(), json_encode($this->getContent()));
	}

	/**
	 * 设置值并设置过期时间
	 * @param string $key 键名
	 * @param string $value 值
	 * @param int $expire 过期时间
	 * @return boolean 固定TRUE
	 */
	public function setWithExpire($key, $value, $expire = 0) {
		$this->content[$key] = array('value'=>$value, 'expire'=>($expire ? time() + $expire : -1));
		$this->saveContent();
		return TRUE;
	}

	/**
	 * 获取并且检查过期时间
	 * @param string $key 键名
	 * @param mixed $default 找不到返回该值
	 * @return string|mixed
	 */
	public function get($key, $default = NULL) {
		// 内容不存在
		if(empty($this->content[$key])) {
			return $default;
		}

		// 已经过期
		if($this->content[$key]['expire'] != -1 && $this->content[$key]['expire'] < time()) {
			unset($this->content[$key]);
			$this->saveContent();
			return $default;
		}

		return $this->content[$key]['value'];
	}
}