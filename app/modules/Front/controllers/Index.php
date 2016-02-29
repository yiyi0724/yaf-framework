<?php


class IndexController extends \Base\FrontController
{

	public function indexAction()
	{
		$data = $this->validity();
		
		echo '<pre>';
		print_r($data);
		exit;
	}
}