<?php

/**
 * 图像图像控制器
 * @author enychen
 *
 */
class ImageController extends \Base\AdminController
{
	/**
	 * 验证码获取
	 */
	public function captchaAction()
	{
		// 数据检查
		$data = $this->validity();

		// 生成验证码并输出
		$image = new \Image\Captcha($data['w'], $data['h']);
		$image->setText();
		$image->createLine();
		$code = $image->getCode();
		$image->output();

		// 验证码保存到session中
		$captChaLogic = new \logic\Captcha();
		$captChaLogic->setCaptchaToSession($data['c'], $code);
		
		// 结束运行
		exit();
	}
}