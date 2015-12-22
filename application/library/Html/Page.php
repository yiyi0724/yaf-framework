<?php
/**
 * 分页类
 * @author enychen
 */

namespace Network;

class Page {
	
	/**
	 * 待输出的信息
	 * @var array
	 */
	protected $output = array();
	
	/**
	 * 构建的信息
	 * @var array
	 */
	protected $build = array('page'=>1);
	
	/**
	 * 构造函数
	 */
	public function __construct($count, $number, $button)
	{		
		// 查询的参数
		parse_str($_SERVER['QUERY_STRING'], $query);	
					
		// 当前第几页
		if(isset($query['page'])) {
			$this->build['page'] = $query['page'];
			unset($query['page']);
		}
		$operation = count($query) ? '&' : '';
		$query = http_build_query($query);

		// 固定的url
		$this->build['url'] = "{$_SERVER['REQUEST_SCHEME']}://{$_SERVER['HTTP_HOST']}{$_SERVER['PATH_INFO']}?{$query}{$operation}";
		
		// 总共几条
		$this->build['count'] = $count;

		// 每页显示的条数
		$this->build['number'] = $number;
		
		// 一共有几页
		$this->build['total'] = ceil($count/$number);
		
		// 一共几个按钮
		$this->build['button'] = $btnNumber;;
	}
	
	/**
	 * 生成分页的链接
	 */
	public function create()
	{
		// 释放变量
		extract($this->build);
		
		// 上一页和首页
		if($page > 1) {
			$this->html['first'] = $this->page-1;
		}
		
		// 中间的几页
		$end = $this->page + $this->btnNumber;
		$end = $end > $this->totalPage ? $this->totalPage+1 : $end;
		$start = $end - $this->btnNumber;
		
		for(;$start<$end; $start++) {
			if($start == $this->page) {
				$this->html[] = "<span>{$start}</span>";
			} else {
				$this->html[] = "<a href='{$this->originUrl}{$this->op}page={$start}'>$start</a>";
			}
			
		}
		
		// 下一页和末页
		if($page < $total) {
			$this->html['last'] = $this->page + 1;
		}
	}
}