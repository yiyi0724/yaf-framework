<?php

/**
 * 图片控制器
 * @author enychen
 */

use \image\Captcha as CaptchaLib;
use \services\common\Captcha as CaptchaService;

class ImageController extends \base\BaseController {

	/**
	 * 输出验证码图片
	 * @return void
	 */
	public function captchaAction() {
		// 数据检查
		$request = $this->getVailRequest();

		// 生成验证码
		$captcha = new CaptchaLib();
		$captcha->setCanvasBgColor(55, 62, 74)->show();

		// 保存验证码
		$captchaService = new CaptchaService();
		$captchaService->set($request->get('channel'), $captcha->getCode());

		// 关闭视图
		$this->disView();
	}
}