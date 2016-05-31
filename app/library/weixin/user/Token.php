<?php
/**
 * 获取用户的access_token
 */
namespace \weixin\user;

class Token extends Base {

	private $code = NULL;

	public function __construct($appid, $appSecret) {
		$this->setAppid($appid);
		$this->setAppSecret($appSecret);
	}

	public function setCode($code) {
		$this->code = $code;
	}

	/**
	 * 获取用户网页授权access_token
	 * @return void
	 * @throws \Exception
	 */
	public function getUserAccessToken() {
		if(!$this->code) {
			$this->throws(1023, '请设置code');
		}

		$url = sprintf(API::UESR_ACCESS_TOKEN, $this->appid, $this->appSecret, $this->code);
		$result = json_decode($this->get($url));
		if(isset($result->errcode)) {
			$this->throws($result->errmsg, $result->errcode);
		}
		
		$this->info = $result;
	}
}