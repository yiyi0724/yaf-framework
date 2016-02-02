<?php

namespace Test;

class OneProductModel extends \Base\AppModel
{
	protected $table = 'one_product';
	
	/**
	 * 获取产品信息
	 */
	public function getProduct()
	{
	}
	
	/**
	 * 获取产品的图片信息
	 */
	public function getPicture()
	{
		$pdo = new Pdo();
		
	}
}