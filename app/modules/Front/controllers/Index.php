<?php


class IndexController extends \Base\FrontController
{

	public function indexAction()
	{
		$driver = new \Yaf\Config\Ini(CONF_PATH . "driver.ini", \Yaf\Application::app()->environ());
		$driver = $driver->toArray();
		//$driver['redis']['master']['host'] = '128.0.0.1';
		
		$mysql = \Driver\Redis::getInstance($driver['redis']['master']);
	}
}