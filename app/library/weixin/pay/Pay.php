<?php

/**
 * 微信支付SDK基类
 * @author enychen
 */
namespace weixin\pay;

abstract class Pay extends \weixin\Base {

	/**
	 * 回调数据进行检查
	 * @param string $xml字符串数据
	 * @return array xml解码后的数组
	 */
	protected function verify($result) {
		// 数据来源检查
		if(!$result) {
			throw new \weixin\Exception('来源非法', 9001);
		}

		// 把数据转成xml
		$result = $this->xmlDecode($result);

		// 签名检查
		if($this->sign($result) !== $result['sign']) {
			throw new \weixin\Exception('签名不正确', 9002);
		}

		// 微信方通信是否成功
		if($result['return_code'] != 'SUCCESS') {
			throw new \weixin\Exception($data['return_msg'], 9003);
		}
		
		// 微信业务处理是否失败
		if(isset($result['result_code']) && $result['result_code'] == 'FAIL') {
			throw new \weixin\Exception('业务失败', 9004);
		}

		return $result;
	}
}