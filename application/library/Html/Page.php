<?php

/**
 * 分页类
 * @author enychen
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
		$build = array('page'=>1);
		
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