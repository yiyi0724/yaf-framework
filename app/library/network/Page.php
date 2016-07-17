<?php

/**
 * 分页类
 * @author enychen
 * @version 1.0
 */
namespace network;

class Page {

	protected $page = 1;

	protected $total = 0;

	protected $perNumber = 10;

	protected $perButton = 10;

	protected $build;

	/**
	 * 构造函数
	 * @param int $page 当前页数
	 * @param int $total 总条数
	 */
	public function __construct($page, $total) {
		$this->setPage($page);
		$this->setTotal($total);
		$this->init();
	}

	public function setPage($page) {
		$this->page = $page;
		return $this;
	}

	public function getPage() {
		return $this->page;
	}

	public function setTotal($total) {
		$this->total = $total;
		return $this;
	}

	public function getTotal() {
		return $this->total;
	}

	public function setPerNumber($perNumber) {
		$this->perNumber = $perNumber;
		return $this;
	}

	public function getPerNumber() {
		return $this->perNumber;
	}

	public function setPerButton($perButton) {
		$this->perButton = $perButton;
		return $this;
	}

	public function getPerButton() {
		return $this->perButton;
	}

	public function setBuild($build) {
		$this->build = $build;
		return $this;
	}

	public function getBuild() {
		return $this->build;
	}

	/**
	 * 初始化分页信息
	 * @return void
	 */
	protected function init() {
		// 初始化参数
		$build['page'] = $this->getPage();
		$build['url'] = str_replace($_SERVER['QUERY_STRING'], NULL, $_SERVER['REQUEST_URI']);
		unset($_REQUEST['page']);
		$build['url'] .= sprintf("%s%spage=", http_build_query($_REQUEST), (count($_REQUEST) ? '&' : NULL));
		// 总共几条
		$build['count'] = $this->getTotal();
		// 每页显示的条数
		$build['limit'] = $this->getPerNumber();
		// 一共有几页
		$build['pageTotal'] = ceil($build['count'] / $build['limit']);
		// 一共几个按钮
		$build['button'] = $this->getPerButton();
		// 是否超过
		$build['over'] = $build['page'] > $build['pageTotal'];
		// 返回
		$this->setBuild($build);
	}

	/**
	 * 模式一:居中显示前后的分页
	 * @return array 分页的信息
	 */
	public function showCenter() {
		// 初始化参数
		$build = $this->getBuild();
		
		// 是否超过
		if(!$build['over']) {
			// 首页和上一页
			if($build['page'] > 1) {
				$build['first'] = 1;
				$build['prev'] = $build['page'] - 1;
			}
			
			// 中间的几页
			$step = floor($build['button'] / 2);
			switch(TRUE) {
				case $build['page'] <= $step:
					// 前几页
					$build['start'] = 1;
					$build['end'] = $build['start'] + $build['button'];
					$build['end'] = $build['end'] > $build['pageTotal'] ? $build['pageTotal'] + 1 : $build['end'];
					break;
				case $build['page'] + $step > $build['pageTotal']:
					// 超出末页
					$build['start'] = $build['pageTotal'] - $build['button'] + 1;
					$build['end'] = $build['pageTotal'] + 1;
					break;
				default:
					// 默认算法
					$build['start'] = $build['page'] - $step;
					$build['end'] = ($build['button'] % 2 == 0) ? $build['page'] + $step : $build['page'] + $step + 1;
			}
			
			// 下一页和末页
			if($build['page'] < $build['pageTotal']) {
				$build['next'] = $build['page'] + 1;
				$build['last'] = $build['pageTotal'];
			}
		}
		
		return $build;
	}
}