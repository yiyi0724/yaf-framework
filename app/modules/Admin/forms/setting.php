<?php
class settingForm {	
	/**
	 * 登录验证
	 * @static
	 * @return array
	 */
	public static function adminInput() {
		return array(
			'p'=>array('GET', 'int', FALSE, '页码有误', ['min'=>1], 1), 
		);
	}
}