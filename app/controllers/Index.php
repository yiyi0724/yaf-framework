<?php

class IndexController extends \Base\BaseController
{
	public function indexAction()
	{
		$logic = new \logic\Member\Add();
		echo $this->jsonp($logic->getUserInfo());
		exit;
	}
}