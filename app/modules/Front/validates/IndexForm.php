<?php


class IndexForm
{
	public static function indexRule()
	{
		// 来源 检查方法 是否必须 错误提示 可选项检查 默认值 别名
		$forms['page'] = array('GET', 'number', TRUE, '用户名不正确', ['min'=>8]);
		$forms['time'] = array('GET', 'number', FALSE, '时间格式有误', NULL, date('Y-m-d H:i:s'));
		
		return $forms;
	}
}