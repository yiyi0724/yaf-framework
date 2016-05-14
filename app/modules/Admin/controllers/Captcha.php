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
		// 生成验证码
		$captcha = new \Image\Captcha(122, 50);
		$captcha->setBackgroundColor(55, 62, 74);
		$captcha->setStar(50);
		$captcha->setLine(3);
		$captcha->show();
		
		// 保存验证码
		\logic\Captcha::set('login', $captcha->getCode());
		
		// 关闭视图渲染
		$this->disView();
	}
}