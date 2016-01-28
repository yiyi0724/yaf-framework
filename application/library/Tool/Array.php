<?php

class Arrays
{
	/**
	 * 二维数组转一维数组
	 */
	protected static function toOneDimensions($lists, $key)
	{
		$rescurise = array();
		foreach($lists as $list)
		{
			$rescurise[] = $list[$key];
		}
	
		return array_unique($rescurise);
	}
}