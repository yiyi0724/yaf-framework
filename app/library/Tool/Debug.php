<?php

namespace Tool;

class Debug
{
	/**
	 * 
	 * @param mixed $arr
	 */
	public static function prePrint($arr)
	{
		exit('<pre>'.print_r($arr).'</pre>');
	}
}