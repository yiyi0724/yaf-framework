<?php

/**
 * 首页表单
 * @author eny
 *
 */
class IndexForm {
	public static function indexInput() {
		return array(
			// id => array('请求方式', '检查方式', '是否必须', '错误提示', '可选配置选项', '默认值', '别名');
			'id' => array('GET', 'int', FALSE, '请输入正确的id值', array('min'=>1), 1, 'id >'),
			'page'=>array('GET', 'int', FALSE, '请输入正确的页码值', array('min'=>1), 1),
		);
	}
}