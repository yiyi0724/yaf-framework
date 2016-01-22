<?php

/**
 * 分页类
 * @author enychen
 * 
 * @example
 * 模式一: $pageInfo = \Html\Page::showCenter(每页几条, 共几条, 按钮个数);
 * 
 * 说明:
 * 1. page作为当前页面的参数,会自动解析,无需传入
 * 2. $build数组解释:
 * 	[page] => 2										// 当前第几页
 	[url] => http://www.library.com/?page=			// url前缀,拼接page使用
 	[count] => 205									// 总条数
 	[limit] => 10									// 每页几条
 	[pageTotal] => 21								// 共几页
 	[button] => 8									// 按钮显示个数,不包括上下页和首页个数
 	[over] => 										// 判断是否超过最大页数,比如pageTotal=21,page=22的时候则表示超过页数,不显示分页
 	[first] => 1									// 存在则表示有首页
 	[prev] => 1										// 存在则表示有上一页
 	[start] => 1									// 按钮的起始值
 	[end] => 9										// 按钮的结束值,在for的时候是<end 而不是 <= end 请注意
 	[next] => 3										// 存在则表示有下一页
 	[last] => 21									// 存在则表示有末页
 
 	3. 具体参考 \view\common\page.phtml页面.是一个完整的案例
 */
namespace Html;

class Page
{

	/**
	 * 初始化分页信息
	 * @static
	 * @param int $number 每页显示的条数
	 * @param int $count 总共有几条
	 * @param int $button 一共要显示几页的按钮,默认10个
	 */
	protected static function init($limit, $count, $button = 10)
	{		
		// 初始化参数
		$build['page'] = 1;
		
		// 查询的参数
		parse_str($_SERVER['QUERY_STRING'], $query);
		
		// 当前第几页
		if(isset($query['page']))
		{
			$build['page'] = $query['page'];
			unset($query['page']);
		}
		$operation = count($query) ? '&' : '';
		$query = http_build_query($query);
		
		// 固定的url
		$build['url'] = "{$_SERVER['REQUEST_SCHEME']}://{$_SERVER['HTTP_HOST']}{$_SERVER['PATH_INFO']}?{$query}{$operation}page=";
		
		// 总共几条
		$build['count'] = $count;
		
		// 每页显示的条数
		$build['limit'] = $limit;
		
		// 一共有几页
		$build['pageTotal'] = ceil($count / $limit);
		
		// 一共几个按钮
		$build['button'] = $button;
		
		// 是否超过
		$build['over'] = $build['page'] > $build['pageTotal'];
		
		return $build;
	}

	/**
	 * 模式一:居中显示前后的分页
	 * 生成分页的链接
	 */
	public static function showCenter($limit, $count, $button = 10)
	{
		// 初始化参数
		$build = static::init($limit, $count, $button);
		
		// 是否超过
		if(!$build['over'])
		{
			// 首页和上一页
			if($build['page'] > 1)
			{
				$build['first'] = 1;
				$build['prev'] = $build['page'] - 1;
			}
			
			// 中间的几页
			$step = floor($build['button'] / 2);
			switch(TRUE)
			{
				case $build['page'] <= $step:
					// 前几页
					$build['start'] = 1;
					$build['end'] = $build['start'] + $build['button'];
					$build['end'] = $build['end'] > $build['pageTotal'] ? $build['pageTotal']+1 : $build['end'];
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
			if($build['page'] < $build['pageTotal'])
			{
				$build['next'] = $build['page'] + 1;
				$build['last'] = $build['pageTotal'];
			}
		}
		
		return $build;
	}
}