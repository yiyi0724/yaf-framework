<?php

namespace Tool;

class Debug
{
	public static function echoArr($arr)
	{
		echo '<pre>';
		print_r($arr);
		echo '</pre>';
		exit;
	}
}