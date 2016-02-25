<?php


class IndexController extends FrontController
{

	public function indexAction()
	{
		$data = $this->validity();
	}
}