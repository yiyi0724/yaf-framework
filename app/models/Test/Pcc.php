<?php

namespace Test;

class PccModel extends \BaseModel
{
	protected $table = 'pcc';
	
	public function updateEn()
	{
		$this->field('id, name_cn, name_en');
		$lists = $this->select(self::FETCH_ALL);
		
		foreach($lists as &$list)
		{
			$list['name_en'] = \Tool\Pinyin::encode($list['name_cn']);
			$this->where(['id'=>$list['id']])->update(['name_en'=>$list['name_en']]);
		}
		
		
		
		echo '<pre>';
		print_r($lists);
		exit;
	}
}