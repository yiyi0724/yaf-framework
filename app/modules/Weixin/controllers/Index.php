<?php


class IndexController extends \Base\WeixinController
{

	public function indexAction()
	{
		$params = file_get_contents('php://input');
		
		file_put_contents('/tmp/a.log', print_r($params, TRUE));
		
		exit();
	}
}