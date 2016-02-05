<?php

class IndexController extends \Base\AppController
{
	public function indexAction()
	{
		$data = $this->validate();
		echo '<pre>';
		print_r($data);
		exit;
/* 		$addLogic = new \logic\Member\Add();
		return $this->jsonp($addLogic->getUserInfo());
		exit; */
	}
}