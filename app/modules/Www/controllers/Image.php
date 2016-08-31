<?php

/**
 * 图片控制器
 * @author enychen
 */

use \services\common\Captcha as CaptchaService;

class ImageController extends \base\BaseController {

	/**
	 * 使用全局模板
	 * @var string
	 */
	protected $useMain = FALSE;

	/**
	 * 输出验证码图片
	 * @return void
	 */
	public function captchaAction() {
		$this->disView();
		CaptchaService::create($this->getRequest()->get('channel'));
	}
}