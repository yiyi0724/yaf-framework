<?php

/**
 * 可逆加密/解密
 */
namespace security;

class Encryption {

	/**
	 * 签名
	 * @param array $data 待加密的数组
	 * @param string $signKey 密钥
	 * @return string
	 */
	private static function sign($data, $signKey) {
		// 删除不必要的选项
		foreach($data as $key=>$value) {
			if($key == 'sign' || is_array($value) || is_object($value)) {
				unset($data[$key]);
			}
		}
		// 按照key键排序
		ksort($data);
		// 生成加密字符串
		return md5(sha1(md5(urldecode(http_build_query($data)) . $signKey, TRUE)));
	}

	/**
	 * 加密
	 * @param array $data 待加密的数据数组
	 * @param string $key 密钥
	 * @return string
	 */
	public static function encrypt(array $data, $signKey) {
		// 生成加密字符串
		$data['sign'] = self::sign($data, $signKey);
		// 进行编码成字符串
		$strShuffle = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
		return substr(str_shuffle($strShuffle), 0, 6) . strrev(base64_encode(json_encode($data)));
	}

	/**
	 * 解密
	 * @param string $data 加密过的字符串信息
	 * @param string $key 密钥
	 * @throws \Exception
	 * @return array
	 */
	public static function decrypt($data, $signKey) {
		// 进行解密
		$decode = json_decode(base64_decode(strrev(substr($data, 6))), TRUE);
		// 解密检查
		if(json_last_error() || empty($decode['sign'])) {
			throw new \Exception('解密失败');
		}
		if($decode['sign'] != self::sign($decode, $signKey)) {
			throw new \Exception('签名不一致');
		}
		// 删除签名
		unset($decode['sign']);

		return $decode;
	}
}
