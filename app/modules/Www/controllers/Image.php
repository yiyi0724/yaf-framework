<?php

/**
 * 图片控制器
 * @author enychen
 */

use \services\common\Captcha as CaptchaService;

class ImageController extends \base\BaseController {

	/**
	 * 输出验证码图片
	 * @return void
	 */
	public function captchaAction() {
		// 数据检查
		$request = $this->getVailRequest();

		// 保存验证码
		$captchaService = new CaptchaService();
		$captchaService->create($request->get('channel'));

		// 关闭视图
		$this->disView();
	}
}