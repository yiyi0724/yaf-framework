<?php

class IndexController extends \Base\AppController
{
	public function indexAction()
	{
		$image = new \Image\Captcha();
		$image->draw();
	}
	
	public function captchaAction()
	{
		$image = new \Image\Captcha();
		$image->draw();
		$image->output();
		exit;
	}
}