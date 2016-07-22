<?php

/**
 * 左侧栏目逻辑
 * @author enychen
 */
namespace services\common;

use \services\base\Base;

class Security extends Base {

	/**
	 * 保存验证码
	 * @param string $key 键名
	 * @param string  $value 值
	 * @return boolean
	 */
	public function set($key, $value) {
		return $this->getSession()->set($key, $value);
	}
}