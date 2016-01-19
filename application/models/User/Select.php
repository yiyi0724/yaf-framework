<?php

namespace User;

use \Base\BaseModel;

class SelectModel extends BaseModel
{
	public function getProduct()
	{
		$product = $this->mysql->table('one_product')->limit(0, 20)->order('id DESC')->select()->fetchAll();
		
		echo '<pre>';
		print_r($product);
		exit();
	}

	/**
	 * 获取图片的信息
	 */
	public function getPicture($lists)
	{
		$pictures = $this->getSupplement($lists, ['one_picture'=>['mark', 'mark']]);
		foreach($pictures as $picture)
		{
			foreach($lists as $key=>$list)
			{
				if($picture['mark'] != $list['mark'])
				{
					continue;
				}
				
				switch($picture['thumb'])
				{
					case 1:
						$lists[$key]['thumb'] = $picture['url'];
						break;
					case 2:
						$lists[$key]['carousel'][] = $picture['url'];						
						break;
					case 3:
						$lists[$key]['small'] = $picture['url'];
						break;
				}
			}
		}
		
		return $lists;
	}
}