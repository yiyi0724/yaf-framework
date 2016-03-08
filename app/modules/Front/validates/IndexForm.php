<?php


class IndexForm
{
	public static function index()
	{
		// 来源 检查方法 是否必须 错误提示 可选项检查 默认值 别名
		$forms['time'] = array('GET', 'number', FALSE, '时间格式有误', NULL, date('Y-m-d H:i:s'));
		
		return $forms;
	}
}