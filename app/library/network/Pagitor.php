<?php

/**
 * 分页类
 * @author enychen
 */
namespace network;

class Pagitor {

	/**
	 * 默认拼接的url地址
	 * @var string
	 */
	protected $url = NULL;

	/**
	 * 原始参数
	 * @var array
	 */
	protected $params;

	/**
	 * 当前第几页
	 * @var int
	 */
	protected $nowPage = 1;

	/**
	 * 总共条数
	 * @var int
	 */
	protected $totalNumber = 0;

	/**
	 * 总共几页
	 * @var int
	 */
	protected $totalPage = 0;

	/**
	 * 每页几条
	 * @var int
	 */
	protected $eachNumber = 10;

	/**
	 * 显示的按钮个数
	 * @var int
	 */
	protected $buttonNUmber = 10;

	/**
	 * 构造函数
	 * @param int $page 当前页数
	 * @param int $total 总条数
	 * @param int $preNumber 每页显示的条数，默认是10
	 */
	public function __construct($nowPage, $totalNumber) {
		$this->setNowPage($page);
		$this->setTotalNumber($totalNumber);
		$this->setParams($_GET);
		$this->setUrl();
	}

	/**
	 * 设置当前页
	 * @param int $page 当前页码
	 * @return Page $this 返回当前对象进行连贯操作
	 */
	protected function setNowPage($nowPage) {
		$this->nowPage = $nowPage;
		return $this;
	}
	
	/**
	 * 获取当前页
	 * @return int
	 */
	public function getNowPage() {
		return $this->nowPage;
	}

	/**
	 * 设置总条数
	 * @param int $totalNumber 总条数
	 * @return Page $this 返回当前对象进行连贯操作
	 */
	protected function setTotalNumber($totalNumber) {
		$this->totalNumber = $totalNumber;
		return $this;
	}

	/**
	 * 获取总条数
	 * @return int
	 */
	public function getTotalNumber() {
		return $this->totalNumber;
	}

	/**
	 * 设置原始参数
	 * @param array $params $_GET | $_POST | $_REQUEST 或者其他自定义数组
	 * @return Page $this 返回当前对象进行连贯操作
	 */
	public function setParams(array $params) {
		unset($params['page']);
		$this->params = $params;
		return $this;
	}

	/**
	 * 获取原始参数
	 * @return array
	 */
	public function getParams() {
		return $this->params;
	}

	/**
	 * 设置前缀url
	 * @return Page $this 返回当前对象进行连贯操作
	 */
	protected function setUrl() {
		$this->url = sprintf("%s?", str_replace("?{$_SERVER['QUERY_STRING']}", NULL, $_SERVER['REQUEST_URI']));
		return $this;
	}

	/**
	 * 获取前缀url
	 * @return string
	 */
	protected function getUrl() {
		return $this->url;
	}

	/**
	 * 设置每页显示的条数
	 * @param int $eachNumber 每页小时条数
	 * @return Page $this 返回当前对象进行连贯操作
	 */
	public function setEachNumber($eachNumber) {
		$this->eachNumber = $eachNumber;
		return $this;
	}
	
	/**
	 * 获取每页显示的条数
	 * @return int
	 */
	public function getEachNumber() {
		return $this->eachNumber;
	}	

	/**
	 * 设置按钮最大个数
	 * @param int $buttonNUmber 按钮最大个数
	 * @return Page $this 返回当前对象进行连贯操作
	 */
	public function setButtonNumber($buttonNUmber) {
		$this->buttonNUmber = $buttonNUmber;
		return $this;
	}

	/**
	 * 获取按钮最大个数
	 * @return int
	 */
	public function getButtonNumber() {
		return $this->buttonNUmber;
	}

	/**
	 * 设置总页数
	 * @param int $totalPage 总页数
	 * @return Page $this 返回当前对象进行连贯操作
	 */
	protected function setTotalPage($totalPage) {
		$this->totalPage = $totalPage;
		return $this;
	}

	/**
	 * 获取总页数
	 * @return int
	 */
	public function getTotalPage() {
		return $this->totalPage;
	}

	/**
	 * 进行分页前的计算
	 * @return Page $this 返回当前对象进行连贯操作
	 */
	protected function calc() {
		// 计算总页数
		$this->setTotalPage(ceil($this->getTotalNumber()/$this->getEachNumber()));
	}

	/**
	 * 组织页码的url地址
	 * @param int $page 页码
	 * @return string
	 */
	protected function buildHref($page) {
		$params = $this->getParams();
		$params['page'] = $params;
		return sprintf("%s%s", $this->getUrl(), http_build_query($params));
	}

	/**
	 * 设置首页按钮
	 */
	public function setFirst() {		
	}

	/**
	 * 设置末页按钮
	 */
	public function setLast() {		
	}

	/**
	 * 设置上一页按钮
	 */
	public function setPrev() {		
	}

	/**
	 * 设置下一页按钮
	 */
	public function setNext() {
	}

	/**
	 * 模式一:居中显示前后的分页
	 * @return array 分页的信息
	 */
	public function showCenter() {
	}

	/**
	 * 模式二：一排一排的翻页
	 */
	public function showFixed() {
		
	}
}