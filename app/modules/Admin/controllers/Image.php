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
		// 访问控制
		IS_GET || $this->jsonp('非法访问', 200);
		
		// 数据检查
		$data = $this->validity();
		
		// 生成验证码
		$image = new \Image\Captcha(100,33);
		$image->setText();
		$image->createLine();
		$code = $image->getCode();
		$this->getSession()->set($data['channel'], $code);
		$image->output();
	}
}