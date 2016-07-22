<?php

/**
 * 验证码逻辑类
 * @author enychen
 */
namespace services\common;

use \services\base\Base;
use \image\Captcha as CaptchaLib;

class Captcha extends Base {

	/**
	 * 生成验证码图片并将验证码值保存到session
	 * @return boolean
	 */
	public function create($key) {
		$captcha = new CaptchaLib();
		$captcha->setCanvasBgColor(55, 62, 74)->show();
		return $this->getSession()->set($key, $captcha->getCode());
	}

	/**
	 * 验证码比较
	 * @param string $key 键名
	 * @param string $code 传递过来的code
	 * @return boolean
	 */
	public function compare($key, $code) {
		$isMatch = !strcasecmp($this->getSession()->get($key), $code);
		$this->getSession()->del($key);
		return $isMatch;
	}
}