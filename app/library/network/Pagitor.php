<?php

/**
 * 分页类
 * @author enychen
 * @version 1.0
 */
namespace network;

class Pagitor {

	/**
	 * 当前第几页
	 * @var int
	 */
	protected $page = 1;

	/**
	 * 总共几页
	 * @var int
	 */
	protected $total = 0;

	/**
	 * 每页几条
	 * @var int
	 */
	protected $perNumber = 10;

	/**
	 * 显示的按钮个数
	 * @var int
	 */
	protected $perButton = 10;

	/**
	 * 建立分页必须的信息
	 * @var array
	 */
	protected $build;

	/**
	 * 构造函数
	 * @param int $page 当前页数
	 * @param int $total 总条数
	 */
	public function __construct($page, $total) {
		$this->setPage($page);
		$this->setTotal($total);
	}

	/**
	 * 设置当前第几页
	 * @param int $page 当前页数
	 * @return Page $this 返回当前对象进行连贯操作
	 */
	public function setPage($page) {
		$this->page = $page;
		return $this;
	}

	/**
	 * 获取当前第几页
	 * @return int
	 */
	public function getPage() {
		return $this->page;
	}

	/**
	 * 设置总条数
	 * @param int $total 总条数
	 * @return Page $this 返回当前对象进行连贯操作
	 */
	public function setTotal($total) {
		$this->total = $total;
		return $this;
	}

	/**
	 * 获取总条数
	 * @return int
	 */
	public function getTotal() {
		return $this->total;
	}

	/**
	 * 设置每页条数
	 * @param int $perNumber 每页几条
	 * @return Page $this 返回当前对象进行连贯操作
	 */
	public function setPerNumber($perNumber) {
		$this->perNumber = $perNumber;
		return $this;
	}

	/**
	 * 获取每页条数
	 * @return int
	 */
	public function getPerNumber() {
		return $this->perNumber;
	}

	/**
	 * 设置要显示的按钮个数
	 * @param int $perButton 按钮个数
	 * @return Page $this 返回当前对象进行连贯操作
	 */
	public function setPerButton($perButton) {
		$this->perButton = $perButton;
		return $this;
	}

	/**
	 * 获取要显示的按钮个数
	 * @return int
	 */
	public function getPerButton() {
		return $this->perButton;
	}

	/**
	 * 建立分页的信息
	 * @return Page $this 返回当前对象进行连贯操作
	 */
	protected function setBuild() {
		// 初始化参数
		$this->build['page'] = $this->getPage();
		$this->build['url'] = str_replace($_SERVER['QUERY_STRING'], NULL, $_SERVER['REQUEST_URI']);
		unset($_REQUEST['page']);
		$this->build['url'] .= sprintf("%s%spage=", http_build_query($_REQUEST), (count($_REQUEST) ? '&' : NULL));
		// 总共几条
		$this->build['count'] = $this->getTotal();
		// 每页显示的条数
		$this->build['limit'] = $this->getPerNumber();
		// 一共有几页
		$this->build['pageTotal'] = ceil($this->build['count'] / $this->build['limit']);
		// 一共几个按钮
		$this->build['button'] = $this->getPerButton();
		// 是否超过
		$this->build['over'] = $this->build['page'] > $this->build['pageTotal'];

		return $this;
	}

	/**
	 * 获取分页的信息
	 * @return array
	 */
	protected function getBuild() {
		return $this->build;
	}

	/**
	 * 模式一:居中显示前后的分页
	 * @return array 分页的信息
	 */
	public function showCenter() {
		// 初始化参数
		$build = $this->setBuild()->getBuild();
		
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