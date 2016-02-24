<?php


class TestController extends \Base\IndexController
{

	public function indexAction()
	{
		ECHO __METHOD__;exit;
	}
	
	public function testAction()
	{
		echo 'trest';exit;
	}
}