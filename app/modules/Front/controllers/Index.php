<?php


class IndexController extends \Base\IndexController
{

	public function indexAction()
	{
		echo 11111;exit;
	}

	public function captchaAction()
	{
		$image = new \Image\Captcha();
		$image->draw();
		$image->output();
		exit();
	}

	public function markdownAction()
	{
	}
}