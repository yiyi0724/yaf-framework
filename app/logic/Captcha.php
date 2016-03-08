<?php

/**
 * 验证码操作逻辑
 * @author enychen
 */
namespace logic;

class Captcha extends Logic
{	
	/**
	 * 把验证码写到session中
	 * @param string $channel 验证码频道
	 * @param string $code 验证码
	 */
	public function setCaptchaToSession($channel, $code)
	{
		$this->getSession()->set($channel, $code);
	}
	
	/**
	 * 检查登录验证码是否正确
	 * @param string $channel 验证码频道
	 * @param string $code 验证码
	 */
	public function checkCodeFromSession($channel, $code)
	{
		$sessionCode = $this->getSession()->get($channel);
		if(strcasecmp($code, $sessionCode))
		{
			return FALSE;
		}
		
		$this->getSession()->del($channel);
		return TRUE;
	}
}