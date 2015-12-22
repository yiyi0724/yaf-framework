<?php

class IndexController extends BaseController
{
	public function indexAction()
	{
		echo __METHOD__;
		
		exit;
	}
	
	public function methodAction()
	{
		echo __METHOD__;
		exit;
	}
	
	public function imageAction()
	{
		$image = new \Image\Thumbnail();
		$image->loadSrc('/home/eny/Downloads/iphone6s.jpg')
			  ->setDistSizeByPercentage(0.75)
			  ->create('/home/eny/Downloads/iphone6s_205.jpg')
			  ->destroy();
		exit;
	}
}