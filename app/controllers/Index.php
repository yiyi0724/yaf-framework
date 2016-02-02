<?php

class IndexController extends \Base\AppController
{
	public function indexAction()
	{
		$addLogic = new \logic\Member\Add();
		return $this->jsonp($addLogic->getUserInfo());
		exit;
	}
}