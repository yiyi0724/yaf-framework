<?php
/**
 * 获取用户的access_token
 */
namespace \weixin\user;

class Token extends Base {

	/**
	 * 用户验证后返回的code码
	 * @var string
	 */
	private $code = NULL;

	/**
	 * 构造函数
	 * @param string $appid 公众号appid
	 * @param string $appSecret 公众号appSecret
	 * @return void
	 */
	public function __construct($appid, $appSecret) {
		$this->setAppid($appid);
		$this->setAppSecret($appSecret);
	}

	/**
	 * 设置code码
	 * @param string $code code码
	 * @return void
	 */
	public function setCode($code) {
		$this->code = $code;
	}

	/**
	 * 获取用户的access_token
	 */
	public function getUserAccessToken() {
		
	}

	/**
	 * 获取用户网页授权access_token
	 * @return \stdClass
	 * @throws \Exception
	 */
	public function getUserAccessToken() {
		if(!$this->code) {
			$this->throws(1023, '请先设置用户的code');
		}

		$url = sprintf(\weixin\API::UESR_ACCESS_TOKEN, $this->appid, $this->appSecret, $this->code);
		$result = json_decode($this->get($url));
		if(isset($result->errcode)) {
			$this->throws($result->errmsg, $result->errcode);
		}
		
		return $result;
	}
}