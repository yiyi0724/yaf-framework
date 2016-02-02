<?php

class IndexController extends \Base\AppController
{
	public function indexAction()
	{
		$data = $this->validate();
		$addLogic = new \logic\Member\Add();
		return $this->jsonp($addLogic->getUserInfo());
		exit;
	}
}