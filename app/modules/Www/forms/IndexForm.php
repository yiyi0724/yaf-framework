<?php
class IndexForm {
	
	/**
	 * 登录验证
	 * @static
	 * @return array
	 */
	public static function indexAction() {
		return array(
			'p'=>array('GET', 'int', FALSE, '页码有误', ['min'=>1], 1),
			'id'=>array('GET', 'int', TRUE, 'id有误', ['min'=>1]),
		);
	}
}