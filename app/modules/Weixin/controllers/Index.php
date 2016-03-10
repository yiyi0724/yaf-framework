<?php


class IndexController extends \Base\WeixinController
{

	public function indexAction()
	{
		$params = $this->getRequest()->getParams();
		
		file_put_contents('/tmp/a.log', print_r($params, TRUE));
		
		exit();
	}
}