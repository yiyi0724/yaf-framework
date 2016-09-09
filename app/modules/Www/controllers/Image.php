<?php

/**
 * 图片控制器
 * @author enychen
 */

use \image\CaptchaService;
use \image\Captcha as CaptchaLib;


class ImageController extends \base\BaseController {

	/**
	 * 控制器初始化
	 */
	protected function initController() {
		$this->disView();
	}

	/**
	 * 输出验证码图片
	 */
	public function captchaAction() {
		// 获取渠道信息
		$channel = $this->getRequest()->get('channel');

		// 生成验证码
		$captcha = new CaptchaLib();
		$captcha->setCanvasBgColor(55, 62, 74)->show();

		// 保存验证码
		CaptchaService::save($channel, $captcha->getCode());
	}
}