<?php

class IndexForm
{
	public static function indexRules()
	{
		// key from type require notify options alias induce
		return array(
			array('page', 'GET', 'number', TRUE, '页码有误', ['min'=>1], NULL , date('Y-m-d H:i:s'),NULL)
		);
	}
}