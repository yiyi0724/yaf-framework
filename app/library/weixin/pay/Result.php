<?php

/**
 * 微信支付结果通知
 * @author enychen
 */
namespace weixin\pay;

class Result extends Base {

	/**
	 * 通知数据
	 * @var array
	 */
	protected $data = array();

	/**
	 * 构造函数
	 */
	public function __construct() {
	}

	protected function setData($data) {
		$this->data = $data;
		return $this;
	}

	/**
	 * 获取通知数据
	 * @return array
	 */
	public function getData() {
		return $this->data;
	}

	/**
	 * 检查回调信息是否正确
	 * @return void
	 */
	public function verify() {
		$data = $this->getPush();
		$this->checkSignature($data);
		$this->setData($this->xmlDecode($data));
	}

	/**
	 * 通知微信已经收到通知，让微信不在通知
	 * @param string $needSign 通知内容是否进行加密
	 * @return void
	 */
	public function response($needSign = FALSE) {
		$response = array('return_code'=>'SUCCESS', 'return_msg'=>'OK');
		if($needSign) {
			$response['sign'] = $this->sign($response);
		}
		echo $this->toXml($response);
	}
}