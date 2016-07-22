<?php

/**
 * 图片控制器
 * @author enychen
 */
class ImageController extends \base\BaseController {

	/**
	 * 重置登录页面
	 * @return void
	 */
	public function init() {
		$this->disView();
	}

	/**
	 * 二维码
	 */
	public function captchaAction() {
		$captcha = new \image\Captcha();
		$captcha->show();
	}
}