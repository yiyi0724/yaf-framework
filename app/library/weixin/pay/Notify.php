<?php

/**
 * 微信回调通知检查
 * @author enychen
 */
namespace weixin\pay;

class Notify extends Pay {

	/**
	 * 微信支付回调验证，获取参数信息
	 * @param string $key 需要签名验证请传入商户密钥
	 * @return void
	 */
	public function notify($key = NULL) {
		// 通知微信成功获取返回结果
		$response = array('return_code'=>'SUCCESS', 'return_msg'=>'OK');

		// 是否需要进行签名
		if($key) {
			$this->setKey($key);
			$response['sign'] = $this->sign($response);
		}

		try {
			// 数据来源检查
			$results = $this->verify(file_get_contents('php://input'));

			// 输出收到信息给微信
			echo $this->xmlEncode($response);
			// 将价格转成元（微信的坑）
			$results['total_fee'] /= 100;
		} catch(\weixin\Exception $e) {
			// 错误输出
			$response['return_code'] = 'FAIL';
			$response['return_msg'] = $e->getMessage();
			echo $this->xmlEncode($response);
			throw $e;
		}
		
		return $results;
	}
}