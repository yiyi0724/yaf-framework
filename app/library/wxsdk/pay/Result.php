<?php

/**
 * 微信支付结果通知
 * @author enychen
 */
namespace wxsdk\pay;

class Result extends Base {

	/**
	 * 通知数据
	 * @var array
	 */
	protected $data = array();

	/**
	 * 构造函数
	 * @throws \wxsdk\WxException
	 */
	public function __construct() {
		$data = $this->getPush();
		$this->checkSignature($data);
		$this->setData($this->xmlDecode($data));
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
	 * 通知微信已经收到通知，让微信不再通知
	 * @param string $isSign 通知内容是否进行加密
	 * @return void
	 */
	public function response($isSign = FALSE) {
		$response = array('return_code'=>'SUCCESS', 'return_msg'=>'OK');
		if($isSign) {
			$response['sign'] = $this->sign($response);
		}
		echo $this->toXml($response);
	}
}