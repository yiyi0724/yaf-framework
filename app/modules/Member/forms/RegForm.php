<?php

/**
 * 注册表单
 * @author enychen
 */

class RegForm {

	/**
	 * 增加注册控制器
	 */
	public static function addAction() {
		$form['username'] = array('POST', 'string', TRUE, '用户名不正确', ['min'=>6, 'max'=>32]);
		$form['password'] = array('POST', 'string', TRUE, '密码长度不足', ['min'=>6, 'max'=>20]);
		return $form;
	}
}