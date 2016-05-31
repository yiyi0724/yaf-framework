<?php

/**
 * 微信支付SDK基类
 * @author enychen
 */
namespace weixin\pay;

abstract class Base extends \weixin\Base {
	
	/**
	 * 创建支付业务逻辑对象
	 * @param string $appid 公众号appid
	 * @param string $mchid 商户id
	 * @param string $key 商户密钥
	 * @return void
	 */
	public function __construct($appid, $mchid, $key) {
		$this->setAppid($appid);
		$this->setMchid($mchid);
		$this->setKey(key);
	}

	/**
	 * 回调数据进行检查
	 * @param string $xml字符串数据
	 * @return array xml解码后的数组
	 */
	protected function verify($result) {
		// 数据来源检查
		if(!$result) {
			$this->throws(1090, '来源非法');
		}

		// 把数据转成xml
		$result = $this->xmlDecode($result);

		// 签名检查
		if($this->sign($result) !== $result['sign']) {
			$this->throws(1091, '签名不正确');
		}

		// 微信方通信是否成功
		if($result['return_code'] != 'SUCCESS') {
			$this->throws(1092, $data['return_msg']);
		}
		
		// 微信业务处理是否失败
		if(isset($result['result_code']) && $result['result_code'] == 'FAIL') {
			$this->throws(1093, $result['err_code_des']);
		}

		return $result;
	}
}