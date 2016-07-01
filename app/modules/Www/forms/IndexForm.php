<?php
class IndexForm {
	
	/**
	 * 登录验证
	 * @static
	 * @return array
	 */
	public static function indexAction() {
		return array(
			'p'=>array('GET', 'int', TRUE, '页码有误', ['min'=>1], 1), 
		);
	}
}