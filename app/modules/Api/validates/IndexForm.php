<?php
class IndexForm
{
	public static function indexRules()
	{
		// key from type require notify options alias induce
		return array(
			array('page', 'GET', 'number', FALSE, '页码有误', ['min'=>1], NULL, 3, NULL), 
			array('page', 'POST', 'number', FALSE, '页码有误', ['min'=>1], NULL, 2, NULL));
	}
}