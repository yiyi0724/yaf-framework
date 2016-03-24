<?php

/**
 * 图像图像控制器
 * @author enychen
 *
 */
class CaptchaController extends \Base\AdminController {

	/**
	 * 后台登录验证码获取
	 */
	public function loginAction() {
		// 验证码库
		$imageLib = new \Image\Captcha(122, 50);
		$code = $imageLib->getCode();
		
		// 验证码保存到session中
		$captChaLogic = new \logic\Captcha();
		$captChaLogic->setCaptchaToSession('login', $code);
		
		// 输出验证码图像
		$imageLib->show();
		
		// 结束运行
		exit();
	}
}