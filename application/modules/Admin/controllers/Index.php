<?php

class IndexController extends BaseController
{
	/**
	 * 入口文件
	 */
	public function indexAction()
	{
		$id = $this->getRequest()->get('id','id');
		echo $id,'<hr/>';
		
		echo __DIR__,'<hr>';
		echo __METHOD__;
		exit;
	}
}