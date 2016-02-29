<?php


class IndexController extends \Base\ApiController
{

	public function indexAction()
	{
		echo '<pre>';
		print_r($_GET);
		exit;
	}
}