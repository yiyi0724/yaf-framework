<?php

/**
 * 数组操作封装
 * @author enychen
 */
namespace Tool;

class Arrays {

	/**
	 * 二维数组转一维数组
	 * @param array $lists 二维数组
	 * @param string|int $key 要整合的数据
	 * @param bool $unique 过滤重复选项
	 * @return void
	 */
	public static function toOneDimensions($lists, $key, $unique = TRUE) {
		$rescurise = array();
		foreach($lists as $key=>$list) {
			$rescurise[] = $list[$key];
		}
		
		return $unique ? array_unique($rescurise) : $rescurise;
	}

	/**
	 * 对象转成数组
	 * @param mixed $obj 所有的对象
	 * @return array
	 */
	public static function objectToArray($obj){
		$arr = is_object($obj) ? get_object_vars($obj) : $obj;
		if(is_array($arr)){
			return array_map(__METHOD__, $arr);
		}else{
			return $arr;
		}
	}
}