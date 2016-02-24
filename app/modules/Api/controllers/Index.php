<?php


class IndexController extends \Base\IndexController
{

	public function indexAction()
	{
		echo '<pre>';
		print_r($_GET);
		exit;
	}
}