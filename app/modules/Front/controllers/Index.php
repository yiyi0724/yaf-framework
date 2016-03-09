<?php


class IndexController extends \Base\FrontController
{

	public function indexAction()
	{
		$mysql = \Driver\Redis::getInstance($driver);
	}
}