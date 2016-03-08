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
		
		exit;
		
		// 生成验证码
		$image = new \Image\Captcha(100,33);
		$image->setText();
		$image->createLine();
		$code = $image->getCode();				
		// 验证码保存到session中
		$this->getSession()->set($data['channel'], $code);		
		// 输出验证码图片
		$image->output();
	}
}