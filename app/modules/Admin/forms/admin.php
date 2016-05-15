<?php
class adminForm {	
	/**
	 * 登录验证
	 * @static
	 * @return array
	 */
	public static function indexInput() {
		return array(
			'p'=>array('GET', 'int', FALSE, '页码有误', ['min'=>1], 1), 
		);
	}
}