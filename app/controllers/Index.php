<?php


class IndexController extends \Base\IndexController
{

	public function indexAction()
	{
		echo 1;exit;
		$image = new \Image\Captcha();
		$image->draw();
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