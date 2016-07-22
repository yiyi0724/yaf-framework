<?php

use \image\Captcha as CaptchaLib;
use \services\common\Captcha as CaptchaService;

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
	 * 输出验证码
	 * @return void
	 */
	public function captchaAction() {
		// 生成验证码
		$captcha = new CaptchaLib();
		$captcha->setCanvasBgColor(55, 62, 74)->show();

		// 保存验证码
		$captchaService = new CaptchaService();
		$captchaService->set(CaptchaService::LOGIN_KEY, $captcha->getCode());
	}
}