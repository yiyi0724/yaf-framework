<?php

use Yaf\Bootstrap_Abstract;
use Yaf\Application;
use Yaf\Registry;
use Yaf\Session;

class Bootstrap extends Bootstrap_Abstract
{
	public function _initHeader()
	{
		header('Content-Type:text/html;charset=UTF-8');
	}
}